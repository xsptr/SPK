<?php

/**
 * Database connection setup
 */
if (!$conn = new Mysqli("localhost", "geouser", "GeoGeo789***", "pip")) {
    echo "<h3>Error: Koneksi database gagal!</h3>";
}

/**
 * Page initialize
 */
if (isset($_GET["pages"])) {
    $_PAGES = $_GET["pages"];
} else {
    $_PAGES = "home";
}

/** 
 * Page setup 
 * @param page 
 * @return page filename
 */
function page($pages) {
    return "pages/" . $pages . ".php";
}

/** 
 * Alert notification 
 * @param message, redirection 
 * @return alert notify
 */
function alert($ico, $msg, $to = null) {
    $to = ($to) ? $to : $_SERVER["PHP_SELF"];
    return "<script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            delay: 500,
            timer: 1500
        });

        Toast.fire({
            icon: '{$ico}',
            title: '{$msg}'
        }).then(function() {
    window.location='{$to}';
        });
    </script>";
}