<?php

namespace Controller;

class CAS extends \Controller\BaseController {

    static public $url = '/account/cas';

    static private function RegexLog($xmlString, $subStr) {
        $pattern = sprintf("/<cas:%s>(.*)<\/cas:%s>/i", $subStr, $subStr);
        preg_match($pattern, $xmlString, $matches);
        if (count($matches) > 0) {
            return $matches[1];
        } else {
            return '';
        }
    }

    static public function get() {
        $ticket = self::$request->get('ticket');
        if ($ticket) {
            $CASserver = 'https://auth.szu.edu.cn/cas.aspx/';
            $URL_PATTERN = "%sserviceValidate?ticket=%s&service=%s";
            $SREVICE_URL = sprintf("http://%s%s",
                                   self::$app->request->getHost(),
                                   self::urlFor('account.cas[get]'));
            $url = sprintf($URL_PATTERN, $CASserver, $ticket, $SREVICE_URL);

            $xmlString = file_get_contents($url);
            $name = $_SESSION['cas']['Pname'] = self::RegexLog($xmlString, "Pname");
            $college = $_SESSION['cas']['OrgName'] = self::RegexLog($xmlString, "OrgName");
            $gender = $_SESSION['cas']['SexName'] = self::RegexLog($xmlString, "SexName");
            $szuno = $_SESSION['cas']['StudentNo'] = self::RegexLog($xmlString, "StudentNo");
            $card_id = $_SESSION['cas']['ICAccount'] = self::RegexLog($xmlString, "ICAccount");
            $identityNumber = $_SESSION['cas']['PersonalId'] = self::RegexLog($xmlString, "PersonalId");
            $rankNum = $_SESSION['cas']['RankName'] = self::RegexLog($xmlString, "RankName");

            $isSzunoExist = \Model\Account::isExistBy('szuno', $szuno);
            if ($isSzunoExist) {
                return self::redirect(self::urlFor('account.profile[get]'));
            } else {
                return self::redirect(self::urlFor('account.sign_up[get]'));
            }
        } else {
            return self::render('account/cas.html');
        }
    }
}
