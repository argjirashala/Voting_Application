<?php
require_once("storage.php");
class GroupStorage extends Storage {
    public function __construct() {
        parent::__construct(new JsonIO('grouppolls.json'));
    }
}