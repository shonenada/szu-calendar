<?php

namespace Controller;

class CAS extends \Controller\BaseController {

    static public $url = '/account/cas';
    static public $allow = array(
        'GET' => array('Guest'),
    );

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
            if (!$xmlString) {
                self::flash('验证失败');
                return self::redirect(self::urlFor('account.sign_in[get]'));
            }
            $name = $_SESSION['cas']['Pname'] = self::RegexLog($xmlString, "Pname");
            $college = $_SESSION['cas']['OrgName'] = self::RegexLog($xmlString, "OrgName");
            $gender = $_SESSION['cas']['SexName'] = self::RegexLog($xmlString, "SexName");
            $szuno = $_SESSION['cas']['StudentNo'] = self::RegexLog($xmlString, "StudentNo");
            $cardId = $_SESSION['cas']['ICAccount'] = self::RegexLog($xmlString, "ICAccount");
            $identityNumber = $_SESSION['cas']['PersonalId'] = self::RegexLog($xmlString, "PersonalId");
            $rankNum = $_SESSION['cas']['RankName'] = self::RegexLog($xmlString, "RankName");

            $account = \Model\Account::getBy('identityNumber', $identityNumber);
            if ($account) {
                if (!isset($account->college) || strlen($account->college) == 0) {
                    $account->college = $college;
                }
                if (!isset($account->gender) || strlen($account->gender) == 0) {
                    $account->gender = $gender;
                }
                if (!isset($account->szuno) || strlen($account->szuno) == 0) {
                    $account->szuno = $szuno;
                }
                if (!isset($account->cardId) || strlen($account->cardId) == 0) {
                    $account->cardId = $cardId;
                }
                if (!isset($account->rankNum) || strlen($account->rankNum) == 0) {
                    $account->rankNum = $rankNum;
                }
                $account->login(self::$app);
            } else {
                $accountData = array(
                    'name' => $_SESSION['cas']['Pname'],
                    'college' => $_SESSION['cas']['OrgName'],
                    'gender' => $_SESSION['cas']['SexName'],
                    'szuno' => $_SESSION['cas']['StudentNo'],
                    'cardId' => $_SESSION['cas']['ICAccount'],
                    'identityNumber' => $_SESSION['cas']['PersonalId'],
                    'rankNum' => $_SESSION['cas']['RankName'],
                );
                $account = \Model\Account::factory($accountData);
                $account->save();
                \Model\Account::flush();
                $account->login(self::$app);
            }
            return self::redirect(self::urlFor('account.profile[get]'));
        } else {
            return self::render('account/cas.html');
        }
    }
}
