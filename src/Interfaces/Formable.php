<?php
namespace TypeRocket\Engine7\Interfaces;

interface Formable
{
    /**
     * Get field Value
     */
    public function getFieldValue(mixed $field) : mixed;

    /**
     * Get Form Fields
     *
     * When a Form or Field tries to assess the object
     * This is the data it returns.
     */
    public function getFormFields() : mixed;

    /**
     * Load Old Data Only
     */
    public function oldStore( bool $loadOld = false) : mixed;
}