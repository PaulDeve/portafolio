<?php
// config/roles.php
if (session_status() == PHP_SESSION_NONE) session_start();

function is_admin(){ return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'; }
function is_vendedor(){ return isset($_SESSION['rol']) && $_SESSION['rol'] === 'vendedor'; }
function is_recepcion(){ return isset($_SESSION['rol']) && $_SESSION['rol'] === 'recepcion'; }
function can_manage_products(){ return is_admin() || is_recepcion(); }
function can_manage_users(){ return is_admin(); }
function can_sell(){ return is_admin() || is_vendedor(); }
