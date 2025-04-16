<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Error;
use OneLogin\Saml2\Utils;

class SAMLController extends Controller
{
    protected $auth;

    public function __construct()
    {
        $this->auth = new Auth(config('saml'));
    }

    public function login(Request $request)
    {
        $this->auth->login(); // Redireciona para o Identity
    }

    public function acs(Request $request)
    {
        $this->auth->processResponse();

        $errors = $this->auth->getErrors();

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 500);
        }

        if (!$this->auth->isAuthenticated()) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $userData = $this->auth->getAttributes();
        $nameId = $this->auth->getNameId();

        // Aqui você pode buscar/criar usuário local
        // e realizar login com Auth::login()

        return response()->json([
            'nameId' => $nameId,
            'attributes' => $userData,
        ]);
    }

    public function metadata()
    {
        $settings = $this->auth->getSettings();
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 500);
        }

        return response($metadata, 200)->header('Content-Type', 'text/xml');
    }
}
