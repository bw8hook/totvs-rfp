<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Utils;


class SAMLController extends Controller
{
    protected $auth;

    public function __construct()
    {
        Utils::setSelfProtocol('https');
        Utils::setSelfPort(443);
        Utils::setSelfHost('totvs.bw8.tech');
        Utils::setProxyVars(true);

        if (isset($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST']);
        }

        $this->auth = new Auth(config('saml'));
    }

    public function login(Request $request)
    {
        try {
            $this->auth->login();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function acs(Request $request)
    {
        try {
            $this->auth->processResponse();
            $errors = $this->auth->getErrors();

            if (!empty($errors)) {
                $lastError = $this->auth->getLastErrorReason();
                return response()->json([
                    'errors' => $errors,
                    'last_error_reason' => $lastError
                ], 500);
            }

            if (!$this->auth->isAuthenticated()) {
                return response()->json(['error' => 'Não autenticado'], 401);
            }

            $userData = $this->auth->getAttributes();

            $email = $userData['email'][0];

            $user = User::where('email', $email)->first();

            if (!$user)
            {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
                    ]);
            }

            if ($user->status == "inativo")
            {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'status' => 'Sua conta está inativa. Entre em contato com o administrador.',
                    ]);
            }

            $token = $user->createToken('API token')->plainTextToken;
            session(['sanctum_token' => $token]);


            Auth::login($user);

            $request->session()->regenerate();

            return redirect()->intended(route('knowledge.list'));


        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro no processamento SAML',
                'message' => $e->getMessage(),
                'trace' => app()->environment('local') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function metadata()
    {
        try {
            $settings = $this->auth->getSettings();
            $metadata = $settings->getSPMetadata();
            $errors = $settings->validateMetadata($metadata);

            if (!empty($errors)) {
                return response()->json(['errors' => $errors], 500);
            }

            return response($metadata, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao gerar metadata',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $returnTo = null;
            $nameId = session('saml_name_id');
            $sessionIndex = session('saml_session_index');

            if ($nameId !== null) {
                $this->auth->logout($returnTo, [], $nameId, $sessionIndex);
            } else {
                $this->auth->logout();
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao realizar logout',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
