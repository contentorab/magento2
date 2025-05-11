<?php

namespace Contentor\LocalizationApi\Model\ContentUpdate\Handler;

abstract class AbstractHandler
{
    protected function getAttributeCode(string $id): string
    {
        return substr($id, 0, -4);
    }

    protected function getAttributeCodes(array $fields): array
    {
        $attributeCodes = [];
        foreach ($fields as $field) {
            if ($field['type'] == 'localizable' || $field['type'] == 'creatable') {
                $attributeCodes[] = $this->getAttributeCode($field['id']);
            }
        }
        return $attributeCodes;
    }
}
