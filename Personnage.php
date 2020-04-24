<?php
class Personnage
{
    // ATTRIBUTS
    private $_id;
    private $_nom;
    private $_degats;

    // CONSTANTES
    const CEST_MOI =1;
    const PERSONNAGE_TUE = 2;
    const PERSONNAGE_FRAPPE = 3;
    const DEGATS_RECU = 5;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'. ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function frapper(Personnage $perso)
    {
        if($this->_id == $perso->id())
            return self::CEST_MOI;

        return $perso->recevoirDegats();
    }

    private function recevoirDegats()
    {

        if ($this->_degats >= 100)
            return self::PERSONNAGE_TUE;

        $this->_degats += self::DEGATS_RECU;
        return self::PERSONNAGE_FRAPPE;     
    }

    // SETTERS
    public function setId($id)
    {
        $id = (int)$id;

        if($id > 0) {
            $this->_id = $id;
        }
    }

    public function setNom($nom)
    {
        if(is_string($nom) && strlen($nom) < 50) {
            $this->_nom = $nom;
        }
    }

    public function setDegats($degats)
    {
        $degats = (int)$degats;

        if($degats >= 0 && $degats <= 100) {
            $this->_degats = $degats;
        }
    }

    // GETTERS
    public function id()
    {
        return $this->_id;
    }

    public function nom()
    {
        return $this->_nom;
    }

    public function degats()
    {
        return $this->_degats;
    }
}