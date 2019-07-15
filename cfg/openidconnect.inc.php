<?php
$config_openidconnect = new stdClass();
$config_openidconnect->authorize_url = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/authorize";
$config_openidconnect->token_url = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/token";
$config_openidconnect->introspect_url = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/introspect";

$config_openidconnect->client_id['upes'] = "ZThiZDg1M2ItZGFiMi00";
$config_openidconnect->client_secret['upes'] = "NjMzODZkNWUtZjMyMi00";
$config_openidconnect->redirect_url['upes'] = "https://upes.w3ibm.mybluemix.net/auth/index.php";

// $config_openidconnect->client_id['cord_ut'] = "YTRiMGI3OTQtMjU0OC00";
// $config_openidconnect->client_secret['cord_ut'] = "OGZkNDFlYmQtMGMwZC00";
// $config_openidconnect->redirect_url['cord_ut'] = "https://cord-ut.w3ibm.mybluemix.net/auth/index.php";



// $config_openidconnect->client_id['rob_dev'] = "ZGE0NDYzMTctYmZhNS00";
// $config_openidconnect->client_secret['rob_dev'] = "ZWFhM2U5MTktNDk4NS00";
// $config_openidconnect->redirect_url['rob_dev'] = "https://restdev.w3ibm.mybluemix.net/auth/index.php";
?>