<?php

class Task {
    private $id;
    private $desc;

    function __construct($desc, $id = null) {
        $this->desc = $desc;
        $this->id = $id;
    }

    function getId() {
        return $this->id;
    }

    function getDesc() {
        return $this->desc;
    }

    function setId($new_id) {
        $this->id = $new_id;
    }

    function save() {
        $st = $GLOBALS['DB']->prepare("INSERT INTO tasks (task) VALUES (:desc) RETURNING id;");
        $st->bindParam(':desc', $this->getDesc());
        $st->execute();
        $result_id = $st->fetch(PDO::FETCH_ASSOC);
        $this->setId($result_id['id']);
    }

    static function findById($search_id) {
        $st = $GLOBALS['DB']->prepare("SELECT * FROM tasks WHERE id = :id;");
        $st->bindParam(':id', $search_id);
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    static function getAll() {
        $st = $GLOBALS['DB']->prepare("SELECT * FROM tasks;");
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
