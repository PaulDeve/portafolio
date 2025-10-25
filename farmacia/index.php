<?php
// index.php

// Por ahora, este archivo simplemente redirige al login.
// En el futuro, podría actuar como un controlador frontal (Front Controller).

header('Location: views/auth/login.php');
exit();
