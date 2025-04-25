<?php
namespace LorPHP\Models;

use LorPHP\Core\Model;

class User extends Model {
    protected $schema = [
        'username' => [
            'type' => 'string',
            'rules' => [
                'required' => true,
                'min' => 3,
                'max' => 50
            ]
        ],
        'email' => [
            'type' => 'string',
            'rules' => [
                'required' => true,
                'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ]
        ],
        'age' => [
            'type' => 'int',
            'rules' => [
                'min' => 18
            ]
        ]
    ];
}
