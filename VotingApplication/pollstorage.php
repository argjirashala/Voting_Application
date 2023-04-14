<?php
require_once("storage.php");
class PollStorage extends Storage {
    public function __construct() {
        parent::__construct(new JsonIO('polls.json'));
    }
}