<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**小程序接口**/
/**项目********************************************************/
Route::middleware('throttle:60,1')->group(function() {
    //根据资金获取项目
    Route::get('/selectProjectByReward', 'API\ProjectController@selectProjectByReward');
    //获取总资金 总项目 总人数
    Route::get('/getGeneralInfo', 'API\ProjectController@getGeneralInfo');
    //根据项目编号删除项目
    Route::post('/deleteProjectById', 'API\ProjectController@deleteProjectById');
    //根据项目编号更新状态
    Route::post('/updateStatusByProjectId', 'API\ProjectController@updateStatusByProjectId');
    //根据项目编号更新项目
    Route::post('/updateProjectById', 'API\ProjectController@updateProjectById');
    Route::post('/chooseUserForProject', 'API\ProjectController@chooseUserForProject');
    Route::post('/wantedPublish', 'API\ProjectController@wantedPublish');
    Route::post('/selectProjectByWinbidphone', 'API\ProjectController@selectProjectByWinbidphone');
    //根据手机号获取项目
    Route::get('/selectProjectByPunlishphone', 'API\ProjectController@selectProjectByPunlishphone');
    Route::get('/selectMySubBidedProject', 'API\ProjectController@selectMySubBidedProject');
    Route::get('/selectMyWinBid', 'API\ProjectController@selectMyWinBid');
});

/**用户********************************************************/
//获取所有用户
Route::middleware('throttle:60,1')->group(function() {
    Route::get('/selectAllUser', 'API\UserController@selectAllUser');
    //根据手机号获取用户
    Route::post('/selectUserByPhone', 'API\UserController@selectUserByPhone');
    //根据项目编号获取用户
    Route::post('/selectUserByProjectId', 'API\UserController@selectUserByProjectId');
    //根据地址和技能获取用户
    Route::post('/selectUser', 'API\UserController@selectUser');
    //更新用户
    Route::post('/updateUserInfo', 'API\UserController@updateUserInfo');
    //新增用户
    Route::post('/insertUser', 'API\UserController@insertUser');
    //更新密码
    Route::post('/updatePassword', 'API\UserController@updatePassword');
    //算客登录
    Route::post('/login', 'API\UserController@login');
});

/**企业********************************************************/
Route::middleware('throttle:60,1')->group(function() {
    //查找企业
    Route::get('/selectCompany', 'API\CompanyController@selectCompany');
    //更新企业信息
    Route::post('/updateCompanyInfo', 'API\CompanyController@updateCompanyInfo');
    //新增企业
    Route::post('/insertCompanyReview', 'API\CompanyController@insertCompanyReview');
    //企业登录
    Route::post('/companyLogin', 'API\CompanyController@companyLogin');
});
/**Bid********************************************************/
Route::middleware('throttle:60,1')->group(function() {
    //提交Bid
    Route::post('/submitbid', 'API\BidController@submitbid');
    Route::post('/selectBidInfo', 'API\BidController@selectBidInfo');
});
/**朋友********************************************************/
Route::middleware('throttle:60,1')->group(function() {
    Route::post('/selectfriendByPhone', 'API\FriendController@selectfriendByPhone');
    Route::post('/insertfriendhelp', 'API\FriendController@insertfriendhelp');
    Route::post('/updateFriendList', 'API\FriendController@updateFriendList');
});
/**AD********************************************************/
Route::middleware('throttle:60,1')->group(function() {
    Route::get('/selectAllAD', 'API\ADController@selectAllAD');
    Route::post('/ADpublish', 'API\ADController@ADpublish');
});
/**Upload********************************************************/
Route::middleware('throttle:60,1')->group(function() {
    //上传企业图片
    Route::post('/uploadbussinessimage', 'API\UploadController@uploadbussinessimage');
    //上传商品图片
    Route::post('/uploadadimage', 'API\UploadController@uploadadimage');
});
/**SMS********************************************************/
//获取验证码
Route::middleware('throttle:60,1')->group(function() {
    Route::post('/getCode', 'API\SmsController@getCode');
});
/**Wechat********************************************************/
Route::middleware('throttle:60,1')->group(function() {
    //获取sessionkey
    Route::get('/code2Session', 'API\WechatController@code2Session');
    Route::post('/unifiedOrder', 'API\WechatController@unifiedOrder');
    Route::any('/wechatNotify', 'API\WechatController@wechatNotify')->name('api.wechat.notify');
});


/**????********************************************************/
//????uploadbussinessimage
//login.js????file/serveragreement
//myclick.js????/files/"+e.currentTarget.dataset.url
//serveragreement.js????/image/serveragreement.jpg
//serveragreement.js????/file/serveragreement.docx
//activeshare.js????/image/activebg.jpg
//activeshare.js????/image/activeshare.jpg
//activeshare.js????selectfriendByPhone
//activeshare.js????getOpenid
//ADpublish.js????uploadadimage
//mywantedpublish.js????options.method
