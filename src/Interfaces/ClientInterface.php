<?php

namespace LorPHP\Interfaces;

/**
 * Interface ClientInterface
 * Represents a client
 *
 * @property String $id Unique identifier for the record
 * @property DateTime $createdAt Timestamp of when the record was created
 * @property DateTime $updatedAt Timestamp of when the record was last updated
 * @property Boolean $isActive Whether the record is active
 * @property String|null $modifiedBy Identifier of the user who last modified the record
 * @property String $name Name
 * @property String $email Email
 * @property String $organizationId OrganizationId
 * @property Organization $organization Organization
 * @property Contact $contacts Contacts for this client
 * @property Package $packages Packages associated with this client
 */
interface ClientInterface
{
    /**
     * Get the id
     * @return String
     */
    public function getId();

    /**
     * Set the id
     * @param String $id
     * @return void
     */
    public function setId($id): void;

    /**
     * Get the createdAt
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * Set the createdAt
     * @param DateTime $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt): void;

    /**
     * Get the updatedAt
     * @return DateTime
     */
    public function getUpdatedAt();

    /**
     * Set the updatedAt
     * @param DateTime $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt): void;

    /**
     * Get the isActive
     * @return Boolean
     */
    public function getIsActive();

    /**
     * Set the isActive
     * @param Boolean $isActive
     * @return void
     */
    public function setIsActive($isActive): void;

    /**
     * Get the modifiedBy
     * @return String|null
     */
    public function getModifiedBy();

    /**
     * Set the modifiedBy
     * @param String|null $modifiedBy
     * @return void
     */
    public function setModifiedBy($modifiedBy): void;

    /**
     * Get the name
     * @return String
     */
    public function getName();

    /**
     * Set the name
     * @param String $name
     * @return void
     */
    public function setName($name): void;

    /**
     * Get the email
     * @return String
     */
    public function getEmail();

    /**
     * Set the email
     * @param String $email
     * @return void
     */
    public function setEmail($email): void;

    /**
     * Get the organizationId
     * @return String
     */
    public function getOrganizationId();

    /**
     * Set the organizationId
     * @param String $organizationId
     * @return void
     */
    public function setOrganizationId($organizationId): void;

    /**
     * Get the organization
     * @return Organization
     */
    public function getOrganization();

    /**
     * Set the organization
     * @param Organization $organization
     * @return void
     */
    public function setOrganization($organization): void;

    /**
     * Get the contacts
     * @return Contact
     */
    public function getContacts();

    /**
     * Set the contacts
     * @param Contact $contacts
     * @return void
     */
    public function setContacts($contacts): void;

    /**
     * Get the packages
     * @return Package
     */
    public function getPackages();

    /**
     * Set the packages
     * @param Package $packages
     * @return void
     */
    public function setPackages($packages): void;

    /**
     * Get related organization
     * @return Organization
     */
    public function organization();

    /**
     * Get related contacts
     * @return Contact[]
     */
    public function contacts();

    /**
     * Get related packages
     * @return Package[]
     */
    public function packages();

}
