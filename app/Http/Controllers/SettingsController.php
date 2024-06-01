<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Admob;
use App\Models\Category;
use App\Models\GlobalSettings;
use App\Models\SubscriptionPackage;
use App\Models\Wallpaper;
use Illuminate\Http\Request;
use App\Class\AgoraDynamicKey\RtcTokenBuilder;
//  include './app/Class/AgoraDynamicKey/RtcTokenBuilder.php';

class SettingsController extends Controller
{
    function index()
    {
        $allCategories = Category::count();
        $category = Category::where('type', '0')->count();
        $liveCategory = Category::where('type', '1')->count();
        $allWallpapers = Wallpaper::count();
        $wallpapers = Wallpaper::where('wallpaper_type', '0')->count();
        $liveWallpapers = Wallpaper::where('wallpaper_type', '1')->count();
        return view('index', [
            'allCategories' => $allCategories, 
            'category' => $category, 
            'liveCategory' => $liveCategory, 
            'allWallpapers' => $allWallpapers, 
            'wallpapers' => $wallpapers, 
            'liveWallpapers' => $liveWallpapers, 
        ]);
    }

    public function setting()
    {
        $setting = GlobalSettings::first();
        return view('setting', [
            'setting' => $setting
        ]);
    }

    public function fetchSettings()
    {
        $data = GlobalSettings::first();
        return response()->json([
            'status' => true,
            'message' => 'Fetch Setting',
            'data' => $data,
        ]);
    }

    public function saveSettings(Request $request)
    {
        $setting = GlobalSettings::first();

        if ($setting == null) {
            return response()->json([
                'status' => false,
                'message' => 'setting Not Found',
            ]);
        } 
        if ($request->has('app_name')) {
            $setting->app_name = $request->app_name;
            $request->session()->put('app_name', $setting['app_name']);
        }
        if ($request->has('currency')) {
            $setting->currency = $request->currency;
            $request->session()->put('currency', $setting['currency']);
        }
        $setting->save();
            
        return response()->json([
            'status' => true,
            'message' => 'Setting Updated Successfully',
        ]);

    }

    public function changePassword(Request $request)
    {
        $admin = Admin::where('user_type', 1)->first();
        if ($admin) {
            if ($request->has('user_password')) {
                if ($admin->user_password == $request->user_password) {
                    $admin->user_password = $request->new_password;
                    $admin->save();
                    return response()->json([
                        'status' => true,
                        'message' => 'Change Password',
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Old Password does not match',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Admin not found',
            ]);
        }
    }

    public function subscription()
    {
        $monthlySubscription = SubscriptionPackage::where('package_id', 1)->first();
        $yearlySubscription = SubscriptionPackage::where('package_id', 2)->first();
        return view('subscription', [
            'monthlySubscription' => $monthlySubscription,
            'yearlySubscription' => $yearlySubscription,
        ]);
    }

    public function monthlySubscription(Request $request)
    {
        $monthlySubscription = SubscriptionPackage::where('package_id', $request->package_id)->first();

        if ($monthlySubscription == null) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription Not Found',
            ]);
        }

        $monthlySubscription->android_product_id = $request->android_product_id; 
        $monthlySubscription->ios_product_id = $request->ios_product_id; 
        $monthlySubscription->save();
            
        return response()->json([
            'status' => true,
            'message' => 'Subscription Updated Successfully',
        ]);

    }

    public function yearlySubscription(Request $request)
    {
        $yearlySubscriptionForm = SubscriptionPackage::where('package_id', $request->package_id)->first();

        if ($yearlySubscriptionForm == null) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription Not Found',
            ]);
        }
        
        $yearlySubscriptionForm->android_product_id = $request->android_product_id; 
        $yearlySubscriptionForm->ios_product_id = $request->ios_product_id; 
        $yearlySubscriptionForm->save();
            
        return response()->json([
            'status' => true,
            'message' => 'Subscription Updated Successfully',
        ]);

    }

    function admob()
    {
        $admobAndroid = Admob::where('type', 1)->first();
        $admobiOS = Admob::where('type', 2)->first();
        return view('admob', [
            'admobAndroid' => $admobAndroid,
            'admobiOS' => $admobiOS,
        ]);
    }

    public function admobAndroid(Request $request)
    {
        $admobAndroid = Admob::where('type', $request->type)->first();

        if ($admobAndroid == null) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription Not Found',
            ]);
        } 
        
        $admobAndroid->banner_id = $request->banner_id; 
        $admobAndroid->intersial_id = $request->intersial_id; 
        $admobAndroid->rewarded_id = $request->rewarded_id; 
        $admobAndroid->save();
            
        return response()->json([
            'status' => true,
            'message' => 'Admob Updated Successfully',
        ]);

    }

    public function admobiOS(Request $request)
    {
        $admobAndroid = Admob::where('type', $request->type)->first();

        if ($admobAndroid == null) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription Not Found',
            ]);
        } 
        
      
        $admobAndroid->banner_id = $request->banner_id; 
        $admobAndroid->intersial_id = $request->intersial_id; 
        $admobAndroid->rewarded_id = $request->rewarded_id; 
        $admobAndroid->save();
            
        return response()->json([
            'status' => true,
            'message' => 'Admob Updated Successfully',
        ]);

    }


   
    // API

    public function fetchAllData(Request $request)
    {
        
        $categories = Category::get();
        $wallpapers = Wallpaper::get();
        $subscriptionPackages = SubscriptionPackage::get();
        $admob = Admob::get();

        return response()->json([
            'status' => true,
            'message' => 'Fetch home page data successfully',
            'wallpapers' => $wallpapers, 
            'categories' => $categories,
            'subscriptionPackages' => $subscriptionPackages, 
            'admob' => $admob, 
        ]);
    }
 
}