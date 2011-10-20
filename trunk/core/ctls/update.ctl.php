<?php
class update_ctl extends pagecore{
    
    function _init(){
        //$this->mdl_album = & loader::model('album');
    }
    
    function core(){
        need_login('page');
        $newversion = $this->getRequest('version');

        $software = 'meiupic';
        $version = MPIC_VERSION;
        $revision = '';
        $langset = LANGSET;
        $time = time();
        $hash = md5("{$newversion}{$software}{$version}{$revision}{$langset}{$time}");
        $q = base64_encode("newversion=$newversion&software=$software&version=$version&revision=$revision&langset=$langset&time=$time&hash=$hash");

        $url = CHECK_UPDATE_URL.'?act=update&q='.$q;
        $remotepath = get_remote($url,2);

    }
}