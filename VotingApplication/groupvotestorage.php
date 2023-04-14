<?php
require_once("storage.php");
class GoupVoteStorage extends Storage {
    public function __construct() {
        parent::__construct(new JsonIO('groupvotes.json'));
    }
}