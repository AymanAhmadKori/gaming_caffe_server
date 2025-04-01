<?php 
include 'init.php';

/* Documentation
  Modes : ( ** = require,  T = type )
  - getAll-accounts : returns array
  - - **`limit` T int
  - - **`except` T array

  - search-account : returns object || null
  - - **`by` T string [ email - id ]
  - - ** `email` T string || ** `id` T int

  - ban-account & unBlock-account : returns bool
  - - ** `id` T int

  - set-sub : returns bool
  - - ** `id` T int
  - - ** `plan-id` T int
  - - ** `plan-cycles` T int

  - get-sub: returns object || null
  - - ** `account-id` T int
  
  - cancel-sub: returns bool
  - - ** `account-id` T int
  - - ** `sub-id` T int

  - getAll-subs-history: returns array
  - - ** `limit` 
  - - ** `except` 
  
*/
