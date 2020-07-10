<?php


class Vehicle
{
    public $id;
    public $name;
    public $condition_nu;
    public $retail;
    public $sales;
    public $stock;
    public $mileage;
    public $make;
    public $model;
    public $year;
    public $trim;
    public $color_exterior;
    public $color_interior;
    public $vin;
    public $data;
    public $options;
    public $photo;
    public $savings;

     /**
      * vehicle constructor.
      *
      */
     public function __construct()
     {
         $this->savings = $this->retail - $this->sales;
      }/**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }/**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }/**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }/**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }/**
     * @return mixed
     */
    public function getCondition_nu()
    {
        return $this->condition_nu;
    }/**
     * @param mixed $condition_nu
     */
    public function setCondition_nu($condition_nu)
    {
        $this->condition_nu = $condition_nu;
    }/**
     * @return mixed
     */
    public function getRetail()
    {
        return $this->retail;
    }/**
     * @param mixed $retail
     */
    public function setRetail($retail)
    {
        $this->retail = $retail;
    }/**
     * @return mixed
     */
    public function getSales()
    {
        return $this->sales;
    }/**
     * @param mixed $sales
     */
    public function setSales($sales)
    {
        $this->sales = $sales;
    }/**
     * @return mixed
     */
    public function getStock()
    {
        return $this->stock;
    }/**
     * @param mixed $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }/**
     * @return mixed
     */
    public function getMileage()
    {
        return $this->mileage;
    }/**
     * @param mixed $mileage
     */
    public function setMileage($mileage)
    {
        $this->mileage = $mileage;
    }/**
     * @return mixed
     */
    public function getMake()
    {
        return $this->make;
    }/**
     * @param mixed $make
     */
    public function setMake($make)
    {
        $this->make = $make;
    }/**
     * @return mixed
     */
     public function getModel()
     {
         return $this->model;
     }/**
     * @param mixed $model
     */
     public function setModel($model)
     {
         $this->model = $model;
     }/**
     * @return mixed
     */
     public function getYear()
     {
         return $this->year;
     }/**
     * @param mixed $year
     */
     public function setYear($year)
     {
         $this->year = $year;
     }/**
     * @return mixed
     */
     public function getTrim()
     {
         return $this->trim;
     }/**
     * @param mixed $trim
     */
     public function setTrim($trim)
     {
         $this->trim = $trim;
     }/**
     * @return mixed
     */
    public function getColorExterior()
    {
        return $this->color_exterior;
    }/**
     * @param mixed $color_exterior
     */
    public function setColorExterior($color_exterior)
    {
        $this->color_exterior = $color_exterior;
    }/**
     * @return mixed
     */
    public function getColorInterior()
    {
        return $this->color_interior;
    }/**
     * @param mixed $color_interior
     */
    public function setColorInterior($color_interior)
    {
        $this->color_interior = $color_interior;
    }/**
     * @return mixed
     */
    public function getVin()
    {
        return $this->vin;
    }/**
     * @param mixed $vin
     */
    public function setVin($vin)
    {
        $this->vin = $vin;
    }/**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }/**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }/**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }/**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }/**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }/**
     * @param mixed $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }
    
    public function getSavings() {
        return $this->savings;
    }

}