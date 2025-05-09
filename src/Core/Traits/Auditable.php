<?php
namespace LorPHP\Core\Traits;

use LorPHP\Core\Database;
use LorPHP\Core\Traits\HasUuid;

trait Auditable {
    use HasUuid;

    private $originalAttributes = [];
    private $shouldAudit = true;

    public function initializeAuditable() {
        $this->initializeHasUuid();
        $this->originalAttributes = $this->attributes;
    }

    public function auditEvent(string $event) {
        if (!$this->shouldAudit) {
            return;
        }

        $db = Database::getInstance();
        
        // Get the current authenticated user from the application
        global $app;
        $user = $app->getState('user');
        $userId = $user ? $user->id : null;
        $organizationId = $user ? $user->organization_id : null;

        $auditData = [
            'id' => $this->generateUuid(),
            'user_id' => $userId,
            'organization_id' => $organizationId,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'event' => $event,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // For updates, track what changed
        if ($event === 'update') {
            $changes = $this->getChanges();
            if (!empty($changes)) {
                $auditData['old_values'] = json_encode($changes['old']);
                $auditData['new_values'] = json_encode($changes['new']);
            } else {
                // If nothing changed, don't create an audit record
                return;
            }
        }

        $db->insert('audits', $auditData);
    }

    private function getChanges(): array {
        $changes = ['old' => [], 'new' => []];
        
        foreach ($this->attributes as $key => $value) {
            // Only audit fields that are in the schema
            if (!isset($this->schema[$key])) {
                continue;
            }
            
            $originalValue = $this->originalAttributes[$key] ?? null;
            if ($value !== $originalValue) {
                $changes['old'][$key] = $originalValue;
                $changes['new'][$key] = $value;
            }
        }

        return $changes;
    }

    public function withoutAuditing(callable $callback) {
        $this->shouldAudit = false;
        try {
            $result = $callback($this);
            return $result;
        } finally {
            $this->shouldAudit = true;
        }
    }

    public function getAuditHistory(?int $limit = null): array {
        $db = Database::getInstance();
        $sql = "SELECT a.*, u.name as user_name 
                FROM audits a 
                LEFT JOIN users u ON a.user_id = u.id 
                WHERE a.auditable_type = ? AND a.auditable_id = ? 
                ORDER BY a.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $db->query($sql, [get_class($this), $this->id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
