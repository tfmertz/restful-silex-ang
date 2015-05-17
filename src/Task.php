<?php

class Task {
    private $id;
    private $desc;
    private $complete;

    function __construct($desc, $complete = 'f', $id = null) {
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
        $st = $GLOBALS['DB']->prepare("INSERT INTO tasks (task, complete) VALUES (:desc, 'f') RETURNING id;");
        $st->bindParam(':desc', $this->getDesc());
        $st->execute();
        $result_id = $st->fetch(PDO::FETCH_ASSOC);
        $this->setId($result_id['id']);
    }

    static function completeTask($search_id) {
        $st = $GLOBALS['DB']->prepare("UPDATE tasks SET complete = 't' WHERE id = :id;");
        $st->bindParam(':id', $search_id);
        $st->execute();
    }

    static function markIncomplete($search_id) {
        $st = $GLOBALS['DB']->prepare("UPDATE tasks SET complete = 'f' WHERE id = :id;");
        $st->bindParam(':id', $search_id);
        $st->execute();
    }

    static function deleteTask($search_id) {
        $st = $GLOBALS['DB']->prepare("DELETE FROM tasks WHERE id = :id;");
        $st->bindParam(':id', $search_id);
        $st->execute();
    }

    static function deleteComplete() {
        $st = $GLOBALS['DB']->prepare("DELETE FROM tasks WHERE complete = 't';");
        return $st->execute();
    }

    static function findById($search_id) {
        $st = $GLOBALS['DB']->prepare("SELECT * FROM tasks WHERE id = :id;");
        $st->bindParam(':id', $search_id);
        $st->execute();

        return $st->fetch(PDO::FETCH_ASSOC);
    }

    static function getAll() {
        $st = $GLOBALS['DB']->prepare("SELECT * FROM tasks;");
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
