<?php
namespace App\Controllers;
use App\Models\UserModel;
use App\Models\AbstractCategoryModel;
use App\Models\EventModel;
use App\Models\VoucherModel;
use App\Models\RegistrationModel;
use App\Models\AbstractModel;
use App\Models\SystemSettingModel;
use App\Models\PaymentModel;
use App\Models\ReviewModel;
use App\Models\CertificateModel;
use App\Models\NotificationModel;

class TestController extends BaseController
{
    public function testAllModels()
    {
        echo "<h1>🚀 ALL MODELS TEST</h1>";
        
        $models = [
            'UserModel' => UserModel::class,
            'AbstractCategoryModel' => AbstractCategoryModel::class,
            'EventModel' => EventModel::class,
            'VoucherModel' => VoucherModel::class,
            'RegistrationModel' => RegistrationModel::class,
            'AbstractModel' => AbstractModel::class,
            'SystemSettingModel' => SystemSettingModel::class,
            'PaymentModel' => PaymentModel::class,
            'ReviewModel' => ReviewModel::class,
            'CertificateModel' => CertificateModel::class,
            'NotificationModel' => NotificationModel::class,
        ];
        
        foreach ($models as $name => $class) {
            try {
                echo "<h3>Testing {$name}...</h3>";
                $model = new $class();
                $count = $model->countAllResults(false);
                echo "✅ {$name}: {$count} records<br>";
                
            } catch (Exception $e) {
                echo "❌ {$name}: " . $e->getMessage() . "<br>";
            }
        }
        
        echo "<h2>🎉 ALL MODELS TESTED!</h2>";
    }
}