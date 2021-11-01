<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fresns Installation Language Lines
    |--------------------------------------------------------------------------
    */

    // header
    'title' => '安装',
    'desc' => '安装向导',
    // step 1
    'step1Title' => '歡迎使用 Fresns',
    'step1Desc' => '在開始安裝前，安裝程式必須取得資料庫的相關資訊；而進行安裝時，安裝人員必須能夠提供安裝程式下列資訊。',
    'step1DatabaseName' => '資料庫名稱',
    'step1DatabaseUsername' => '資料庫使用者名稱',
    'step1DatabasePassword' => '資料庫密碼',
    'step1DatabaseHost' => '資料庫主機位址',
    'step1DatabaseTablePrefix' => '資料表前置詞 (用於在單一資料庫中安裝多個 Fresns)',
    'step1DatabaseDesc' => '在多數的狀況中，這些資訊應由網站主機服務商提供。如果安裝人員並未獲得這些資訊，請在繼續安裝前先行聯絡廠商。當一切準備就緒後...',
    'step1Btn' => '開始安裝吧！',
    // step 2
    'step2Title' => '基礎環境檢查',
    'step2Desc' => '',
    'step2CheckPhpVersion' => 'PHP 版本要求 8.0.0 或以上',
    'step2CheckHttps' => '站點推薦使用 HTTPS',
    'step2CheckFolderOwnership' => '目錄權限',
    'step2CheckPhpExtensions' => 'PHP 擴展檢查',
    'step2CheckPhpFunctions' => 'PHP 函數檢查',
    'step2CheckStatusSuccess' => '成功',
    'step2CheckStatusFailure' => '失敗',
    'step2CheckStatusWarning' => '警告',
    'step2StatusNotEnabled' => '未啟用',
    'step2CheckBtn' => '再試一次',
    'step2Btn' => '確認',
    // step 3
    'step3Title' => '填寫資料庫信息',
    'step3Desc' => '資料庫版本要求 MySQL 8.0.0 或以上，安裝人員應於下方輸入資料庫連線詳細資料。如果不清楚以下欄位代表的意義，請洽詢網站主機服務商。',
    'step3DatabaseName' => '資料庫名稱',
    'step3DatabaseNameIntro' => '使用於 Fresns 的網站資料庫名稱。',
    'step3DatabaseUsername' => '使用者名稱',
    'step3DatabaseUsernameIntro' => 'Fresns 網站資料庫的使用者名稱。',
    'step3DatabasePassword' => '密碼',
    'step3DatabasePasswordIntro' => 'Fresns 網站資料庫的密碼。',
    'step3DatabaseHost' => '資料庫主機位址',
    'step3DatabaseHostIntro' => '如果因故無法使用 localhost 進行連線，請要求網站主機服務商提供正確對應資訊。',
    'step3DatabaseTablePrefix' => '資料表前置詞',
    'step3DatabaseTablePrefixIntro' => '如需在同一個資料庫中安裝多個 Fresns，請修改這個欄位中的預設設定。',
    'step3CheckDatabaseFailure' => '建立資料庫連線時發生錯誤，系統無法連線至資料庫主機。',
    'step3Btn' => '傳送',
    // step 4
    'step4Title' => '安裝所需資訊',
    'step4Desc' => '請提供下列資訊。不必擔心，這些設定均可於安裝完成後進行變更。使用者郵箱和手機號可以二選一，也可以全部填寫。',
    'step4BackendHost' => '網站地址',
    'step4MemberNickname' => '使用者名稱',
    'step4AccountEmail' => '電子郵件地址',
    'step4AccountPhoneNumber' => '手機號碼',
    'step4AccountPassword' => '密碼',
    'step4CheckAccount' => '郵箱和手機號碼必須填一個',
    'step4Btn' => '安裝 Fresns',
    // step 5
    'step5Title' => '大功告成！',
    'step5Desc' => 'Fresns 已完成安裝。感謝採用 Fresns，請享用它所帶來的無窮魅力！',
    'step5Account' => '使用者賬號：您填寫的郵箱或手機號（帶國際區號的手機號）',
    'step5Password' => '密碼：您填寫的密碼',
    'step5Btn' => '前往登錄',
];
