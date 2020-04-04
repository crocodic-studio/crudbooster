# Helpers

| Helper                        | Description                 |
| ----------------------------- | --------------------------- |
| CRUDBooster::getSetting($name) | To get the setting. $name you can fill from slug of settings |
| CRUDBooster::myId() | To get your current user id |
| CRUDBooster::isSuperadmin() | To get if you are is superadmin or not |
| CRUDBooster::myName() | To get currently user name |
| CRUDBooster::myPrivilegeId() | To get currently user privilege id |
| CRUDBooster::myPrivilegeName() | To get currently user privilege name |
| CRUDBooster::isView() | To check an access to view, whether you are allowed or not | 
| CRUDBooster::isCreate() | To check an access to create, whether you are allowed or not |
| CRUDBooster::isRead() | To check an access to read, wheter you are allowed or not |
| CRUDBooster::isDelete() | To check an access to delete, whether you are allowed or not |
| CRUDBooster::isCreate() | To check an access to create, whether you are allowed or not |
| CRUDBooster::mainpath($slug=NULL) | To get a module path `e.g : http://localhost/project/public/admin/module_name`|
| CRUDBooster::adminPath($slug=NULL) | To get an Admin Path `e.g : http://localhost/project/public/admin` |
| CRUDBooster::getCurrentMethod() | To get the currently method |
| CRUDBooster::sendEmail($config=[])  | You need to create an email template before use this helper. <br>$data = ['name'=>'John Doe','address'=>'Lorem ipsum dolor...']; CRUDBooster::sendEmail(['to'=>'john@gmail.com',<br>'data'=>$data,'template'=>'order_success','attachments'=>[]]);   |
| CRUDBooster::sendNotification($config=[]) | To create a backend notification<br>$config['content'] = "Hellow World";<br>$config['to'] = CRUDBooster::adminPath('some_module');<br>$config['id_cms_users'] = [1,2,3,4,5]; //This is an array of id users<br>CRUDBooster::sendNotification($config);|
| CRUDBooster::sendFCM($regid,$data) | To send a Google FCM . Before use this helper, please make sure you have setted a Google FCM Server Key at the setting page<br>$regid = ['REGID_GOES_HERE','REGID2_GOES_HERE','ETC...'];<br>$data['title'] = "This is a message title";<br>$data['content'] = "This is a message body";<br>// You can add more key as you need<br>// $data['your_other_key'] =<br>CRUDBooster::sendFCM($regid,$data); |
