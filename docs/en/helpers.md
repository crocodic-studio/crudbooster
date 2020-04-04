# Helpers

| Helper                        | Description                 |
| ----------------------------- | --------------------------- |
| cb()->getSetting($name) | To get the setting. $name you can fill from slug of settings |
| cb()->myId() | To get your current user id |
| cb()->isSuperadmin() | To get if you are is superadmin or not |
| cb()->myName() | To get currently user name |
| cb()->myPrivilegeId() | To get currently user privilege id |
| cb()->myPrivilegeName() | To get currently user privilege name |
| cb()->isView() | To check an access to view, whether you are allowed or not | 
| cb()->isCreate() | To check an access to create, whether you are allowed or not |
| cb()->isRead() | To check an access to read, wheter you are allowed or not |
| cb()->isDelete() | To check an access to delete, whether you are allowed or not |
| cb()->isCreate() | To check an access to create, whether you are allowed or not |
| cb()->mainpath($slug=NULL) | To get a module path `e.g : http://localhost/project/public/admin/module_name`|
| cb()->adminPath($slug=NULL) | To get an Admin Path `e.g : http://localhost/project/public/admin` |
| cb()->getCurrentMethod() | To get the currently method |
| cb()->sendEmail($config=[])  | You need to create an email template before use this helper. <br>$data = ['name'=>'John Doe','address'=>'Lorem ipsum dolor...']; cb()->sendEmail(['to'=>'john@gmail.com',<br>'data'=>$data,'template'=>'order_success','attachments'=>[]]);   |
| cb()->sendNotification($config=[]) | To create a backend notification<br>$config['content'] = "Hellow World";<br>$config['to'] = cb()->adminPath('some_module');<br>$config['id_cms_users'] = [1,2,3,4,5]; //This is an array of id users<br>cb()->sendNotification($config);|
| cb()->sendFCM($regid,$data) | To send a Google FCM . Before use this helper, please make sure you have setted a Google FCM Server Key at the setting page<br>$regid = ['REGID_GOES_HERE','REGID2_GOES_HERE','ETC...'];<br>$data['title'] = "This is a message title";<br>$data['content'] = "This is a message body";<br>// You can add more key as you need<br>// $data['your_other_key'] =<br>cb()->sendFCM($regid,$data); |
