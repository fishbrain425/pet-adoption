<?php

class GetPets {

    function __construct()
    {
        global $wpdb;
        $tablename = $wpdb->prefix . 'pets';
        // to use $tablename in the query, use "" instead of ''
        // $ourQuery = $wpdb->prepare("SELECT * FROM $tablename WHERE species = %s LIMIT 100", array($_GET['species']));
        // $this->pets = $wpdb->get_results($ourQuery);

        //store and sanitize URL arguments
        $this->args = $this->getArgs();
        $this->placeholders = $this->createPlaceholders();
 
        $query = "SELECT * FROM $tablename ";
        
        // concat to string $query = $query + 
        $query .= $this->createWhereText();
        $query .= " LIMIT 100";

        // seperate query for getting the total count
        $countQuery = "SELECT COUNT(*) from $tablename ";
        // concat to string $countQuery = $query + 
        $countQuery .= $this->createWhereText();
        
    
        $this->count = $wpdb->get_var($wpdb->prepare($countQuery,$this->placeholders));
        $this->pets = $wpdb->get_results($wpdb->prepare($query,$this->placeholders));
    }

    function getArgs() {

        $temp = array(
            'favcolor' => sanitize_text_field($_GET['favcolor']),
            'species' => sanitize_text_field($_GET['species']),
            'favhobby' => sanitize_text_field($_GET['favhobby']),
            'favfood' => sanitize_text_field($_GET['favfood']),
            'birthyear' => sanitize_text_field($_GET['birthyear']),
            'petname' => sanitize_text_field($_GET['petname']),
            'minyear' => sanitize_text_field($_GET['minyear']),
            'maxyear' => sanitize_text_field($_GET['maxyear']),
            'minweight' => sanitize_text_field($_GET['minweight']),
            'maxweight' => sanitize_text_field($_GET['maxweight']),
        );

        //2nd parameter of array_filter is inline function. returniing a value of true will include the item in the new array we are building, so if favcolor has a value, it will be returned
        return array_filter($temp, function($x) {
            return $x;
        });

    }

    function createPlaceholders() {


        //array_map takes an inline function that returns inself and $this->args
        return array_map(function($x){
            return $x;
        },$this->args);

    }

    function createWhereText() {

        $whereQuery = "";

        if (count($this->args)) {
            $whereQuery = "WHERE ";

        }

       
       $currentPosition = 0; 
        // get access to property name or key name or args array
        foreach($this->args as $index => $item) {
        
            // loop through the args array and add to $whereQuery
            // specificQuery will handle queries such as ?species = as well as ?minweight = 50 AND ?maxweight = 100
            $whereQuery .= $this->specificQuery($index);


            // if not at the last item of the array, add an AND to the query
            if ($currentPosition != count($this->args) - 1) {
                $whereQuery .= " AND ";
            }
        

        return $whereQuery;
    }
    }

    // %s for text, %d for digit or number
    function specificQuery($index) {

        switch($index) {
            case "minweight":
                return "petweight >= %d";
            case "maxweight":
                return "petweight <= %d";
            case "minyear":
                return "birthyear >= %d";
            case "maxyear":
                return "birthyear <= %d";
            case "petweight":
                return "petweight = %d";
            // case "species"; 
            //     return "species = %s";
            // case "petname";
            //     return "petname = %s";
            // case "favcolor";
            //     return "favcolor = %s";
            // case "favhobby";
            //     return "favhobby = %s";

            // default covers species, petname, favcolor, favhobby, etc
            default;
                return $index . " = %s";
            }
    }

}

