<?php
class PersonnageManager
{
    private $_db;

    public function __construct(PDO $db)
    {
        $this->setDb($db);
    }

    public function add(Personnage $perso)
    {
        $req = $this->_db->prepare(
            'INSERT INTO personnages (nom)
            VALUES (:nom)'
        ) or die(print_r($this->_db->errorInfo()));
        $req->bindValue(':nom', $perso->nom());
        $req->execute();

        $perso->hydrate([
            'id' => $this->_db->lastInsertId(),
            'degats' => 0,
        ]);
    }

    public function count()
    {
        return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
    }

    public function delete(Personnage $perso)
    {
        $this->_db->exec(
            'DELETE FROM personnages WHERE id = '. $perso->id()
        ) or die(print_r($this->_db->errorInfo()));
    }

    public function exists($info)
    {
        if(is_int($info)) {
            return (bool) $this->_db->query('SELECT COUNT(*) FROM personnages WHERE id = '.$info)->fetchColumn();
        }

        $req = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = ?');
        $req->execute([$info]);

        return (bool) $req->fetchColumn();
    }

    public function get($info)
    {
        $q = 'SELECT id, nom, degats FROM personnages WHERE ';
        if(is_int($info)) {
            $q .= 'id = ?';
        } else {
            $q .= 'nom = ?';
        }

        $req = $this->_db->prepare($q) or die(print_r($this->_db->errorInfo()));
        $req->execute([$info]);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        $req->closeCursor();

        return new Personnage($data);
    }

    public function getList($nom)
    {
        $persos = [];

        $req = $this->_db->prepare(
            'SELECT id, nom, degats
            FROM personnages
            WHERE nom <> ?
            ORDER BY nom'
        ) or die(print_r($this->_db->errorInfo()));
        
        $req->execute([$nom]);

        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $persos[] = new Personnage($data);
        }
        $req->closeCursor();

        return $persos;
    }

    public function update(Personnage $perso)
    {
        $req = $this->_db->prepare(
            'UPDATE personnages
            SET degats = :degats
            WHERE id = :id'
        ) or die(print_r($this->_db->errorInfo()));

        $req->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
        $req->bindValue(':id', $perso->id(), PDO::PARAM_INT);

        $req->execute();
    }
    
    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
}