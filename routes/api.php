<?php

use App\Models\Admin\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\EpriceController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\PpriceController;
use App\Http\Controllers\Admin\QrcodeController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\MyPaymentController;
use App\Http\Controllers\Admin\MyProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\Admin\Vote\VoteController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\Vote\ChoiceController;
use App\Http\Controllers\Admin\Setting\AdminController;
use App\Http\Controllers\Admin\Statistics\PostStatsController;
use App\Http\Controllers\Admin\Statistics\ProductStatsController;
use App\Http\Controllers\Admin\Statistics\MstatisticsController;
use App\Http\Controllers\Admin\Statistics\VstatisticsController;
use App\Http\Controllers\Admin\Statistics\ParticipantStatsController;
use App\Http\Controllers\Admin\Statistics\PaymentStatsController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('agreement', [AuthController::class, 'agreement']); //개인약관 동의
Route::get('/login/{provider}', [AuthController::class,'redirectToProvider']); //웹 SNS로그인시 필요
Route::get('/login/sns/callback', [SocialiteController::class,'handleProviderCallback']);
Route::post('/login/sns/callback', [SocialiteController::class,'handleProviderCallback']);
Route::get('countries', [AuthController::class, 'countryList']);
Route::post('forget-password', [AuthController::class,'forgetPassword']);
Route::get('reset-password',[AuthController::class, 'resetPasswordLoad']);
Route::post('reset-password',[AuthController::class, 'resetPassword']);
Route::post('email-verify', [AuthController::class,'emailVerify']);
Route::get('register-stats', [DashBoardController::class, 'registerStatistics']);




//로그인 체크
Route::prefix('auth')->middleware('auth:sanctum')->group(function () {

    //AUTH
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'show']);

    // Route::post('profile', [AuthController::class, 'profile']);
    Route::get('qrcode', [QrcodeController::class, 'qrcodeMe']);
    Route::get('qrcode-event', [QrcodeController::class, 'qrcodeEvent']);
    Route::get('profile', [AuthController::class, 'showProfile']);
    Route::get('me', [AuthController::class, 'show']);
    Route::post('reset-mypassword', [AuthController::class, 'myResetPassword']);
    Route::post('profile-image', [AuthController::class, 'storeImage']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
    Route::delete('profile', [AuthController::class, 'destroyProfile']);
    // Route::put('image', [AuthController::class, 'destroyImage']);
    Route::delete('me', [AuthController::class, 'delete']);
    Route::get('alert', [AuthController::class, 'alert']);
    Route::get('lang', [AuthController::class, 'lang']);
    Route::post('alert', [AuthController::class, 'updateAlert']);
    Route::post('lang', [AuthController::class, 'updateLang']);
    Route::get('admin-payment', [AuthController::class, 'adminPayment']);
    Route::get('payment-history', [AuthController::class, 'paymentHistory']);
    Route::get('sell-history', [AuthController::class, 'sellHistory']);
    Route::get('event-history', [AuthController::class, 'eventHistory']);
    Route::post('membership', [AuthController::class, 'storeMembership']);
    Route::get('membership', [AuthController::class, 'membership']);

    Route::post('support', [SupportController::class, 'supportStore']);
    Route::post('promotion', [SupportController::class, 'promotionStore']);

    Route::apiResource('carts', CartController::class);
    Route::put('carts', [CartController::class, 'payProducts']);

});


// prefix('admin') => admin/{name}
Route::prefix('admin')->middleware('auth:sanctum')->group(function(){

    Route::apiResource('countries', CountryController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('payments', PaymentController::class)->only('index', 'show', 'destroy');

    //members info route
    Route::apiResource('members', MemberController::class);

    //faq info route
    Route::apiResource('faqs', FaqController::class);
    //history
    Route::get('members/history/{member}', [MemberController::class, 'history']);

    //trashed members info route
    Route::get('trash/members', [MemberController::class, 'showTrash']);
    Route::put('trash/members/{id}', [MemberController::class,'restoreTrash']);
    Route::delete('trash/members/{id}', [MemberController::class,'destroyTrash']);


    //post route
    Route::apiResource('posts', PostController::class);
    Route::post('posts/images', [PostController::class, 'storeImage']);
    Route::apiResource('posts.eprices', EpriceController::class);
    Route::apiResource('participants', ParticipantController::class)->only('index', 'show', 'destroy');

    //trashed post route
    Route::get('trash/posts', [PostController::class, 'showTrash']);
    Route::put('trash/posts/{id}', [PostController::class, 'restoreTrash']);
    Route::delete('trash/posts/{id}', [PostController::class, 'destroyTrash']);


    //product route
    Route::apiResource('products', ProductController::class);
    Route::apiResource('products.pprices', PpriceController::class);

    //my product route
    Route::apiResource('myproducts', MyProductController::class);    
    //my payment route
    Route::apiResource('mypayments', MyPaymentController::class)->only('index', 'show', 'destroy');
        

    //voting route
    Route::apiResource('votes', VoteController::class);
    Route::apiResource('votes.choices', ChoiceController::class);
    Route::post('/votes/order/{id}', [ChoiceController::class,'order']);
    Route::post('/votes/{vote}/voter', [VoteController::class,'voter']);
    Route::get('/voter/{id}', [VoteController::class,'voterList']); //TODO 새로 추가된 route voter list 불러오기

    //statistics route
    Route::get('/statistics/vstatistics', [VstatisticsController::class, 'index']);
    Route::get('/statistics/mstatistics', [MstatisticsController::class, 'index']);
    Route::get('/statistics/poststatistics', [PostStatsController::class, 'index']);
    Route::get('/statistics/productstatistics', [ProductStatsController::class, 'index']);
    Route::get('/statistics/participantstats', [ParticipantStatsController::class, 'index']);
    Route::get('/statistics/paymentstats', [PaymentStatsController::class, 'index']);
    Route::get('/statistics/stats', [DashBoardController::class, 'stats']); //해결하기
    Route::get('/statistics/stats-event', [DashBoardController::class, 'statsEvent']); //해결하기

    //Qrcode route
    Route::apiResource('qrcodeinfos', QrcodeController::class)->only('index', 'show');

    //Notice route
    Route::apiResource('notices', NoticeController::class);

    //Banner route
    Route::apiResource('banners', BannerController::class);

    // Route::apiResource('medias', MediaController::class);

    //설정
    //메뉴 관리
    //roles and permissions and "admin"
    Route::get('init-app', function () {
        Menu::setPermissions();
        $menus = Menu::getMenus();
        return compact(['menus']);
    });

    //권한 관리
    Route::apiResource('setting/admin', AdminController::class);
});

//Route for home
include 'home.php';
