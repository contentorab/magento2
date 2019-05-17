<?php
namespace Contentor\LocalizationApi\Api\Data;

interface TypeInterface
{
    /**#@+
     * Column names
     * @var string
     */
    const CONTENTOR_ID = 'contentor_id';
    const TYPE  = 'type';
    /**#@-*/

    /**
     * @param  int $contentorId
     * @return void
     */
    public function setContentorId($contentorId);

    /**
     * @return int
     */
    public function getContentorId();

    /**
     * @param string $type
     * @return void
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();
}