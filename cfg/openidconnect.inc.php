<?php
$config_openidconnect = new stdClass();
// $config_openidconnect->authorize_url = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/authorize";
// $config_openidconnect->token_url = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/token";
// $config_openidconnect->introspect_url = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/introspect";

// $config_openidconnect->client_id['vbac'] = "NjVhM2FkZjQtNmU2Ny00";
// $config_openidconnect->client_secret['vbac'] = "MzJkYjc5YjktNjBhYy00";
// $config_openidconnect->redirect_url['vbac'] = "https://vbac.w3ibm.mybluemix.net/auth/index.php";

// $config_openidconnect->client_id['vbac_ut'] = "ZWMwMTNkYTEtYmEzMC00";
// $config_openidconnect->client_secret['vbac_ut'] = "ZWVlYzZjYzktNzUwOS00";
// $config_openidconnect->redirect_url['vbac_ut'] = "https://vbac-ut.w3ibm.mybluemix.net/auth/index.php";

// $config_openidconnect->client_id['rob_dev'] = "ZGE0NDYzMTctYmZhNS00";
// $config_openidconnect->client_secret['rob_dev'] = "ZWFhM2U5MTktNDk4NS00";
// $config_openidconnect->redirect_url['rob_dev'] = "https://restdev.w3ibm.mybluemix.net/auth/index.php";



/*
 * SSO Element of Config
 *
 */

$config_openidconnect->client_id['staging']      = "ZWMwMTNkYTEtYmEzMC00";
$config_openidconnect->client_secret['staging']  = "ZWVlYzZjYzktNzUwOS00";

$config_openidconnect->authorize_url['staging']  = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/authorize";
$config_openidconnect->token_url['staging']      = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/token";
$config_openidconnect->introspect_url['staging'] = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/introspect";


// $config_openidconnect->client_id['production']      = "";
// $config_openidconnect->client_secret['production']  = "";

// $config_openidconnect->authorize_url['production']  = "https://w3id.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/authorize";
// $config_openidconnect->token_url['production']      = "https://w3id.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/token";
// $config_openidconnect->introspect_url['production'] = "https://w3id.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/introspect";

/*
 * Application Instance of Config
 *
 */

$config_openidconnect->redirect_url = "https://" . $_SERVER['HTTP_HOST'] . "/auth/index.php";




?>