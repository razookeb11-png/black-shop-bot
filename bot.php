<?php

$token = "8698840173:AAHNRrDPvEWW0XuDfyM_tlNXSBBw_flL158";
$admin_id = 8698840173;

$api = "https://api.telegram.org/bot$token/";

# فایل ذخیره کاربران
$file = "users.json";

if(!file_exists($file)){
    file_put_contents($file, json_encode([]));
}

$users = json_decode(file_get_contents($file), true);

# تابع ربات
function bot($method, $data = [])
{
    global $api;

    $url = $api . $method;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $res = curl_exec($ch);

    curl_close($ch);

    return json_decode($res, true);
}

# دریافت آپدیت
$update = json_decode(file_get_contents("php://input"), true);

# پیام ها
if(isset($update["message"])){

    $message = $update["message"];

    $chat_id = $message["chat"]["id"];
    $text = $message["text"] ?? "";
    $first_name = $message["from"]["first_name"];

    # استارت
    if($text == "/start"){

        $keyboard = [
            "keyboard" => [
                [["text" => "🟢 خرید OpenVPN"]],
                [["text" => "🔵 خرید V2rayNG"]]
            ],
            "resize_keyboard" => true
        ];

        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "سلام 👋\nسرویس موردنظر را انتخاب کنید.",
            "reply_markup" => json_encode($keyboard)
        ]);
    }

    # منوی OPENVPN
    elseif($text == "🟢 خرید OpenVPN"){

        $keyboard = [
            "inline_keyboard" => [

                [
                    [
                        "text" => "📦 5 گیگ | 1,800,000",
                        "callback_data" => "ovpn_5"
                    ]
                ],

                [
                    [
                        "text" => "📦 10 گیگ | 3,000,000",
                        "callback_data" => "ovpn_10"
                    ]
                ]

            ]
        ];

        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "پلن موردنظر OpenVPN را انتخاب کنید:",
            "reply_markup" => json_encode($keyboard)
        ]);
    }

    # منوی V2RAY
    elseif($text == "🔵 خرید V2rayNG"){

        $keyboard = [
            "inline_keyboard" => [

                [
                    [
                        "text" => "📦 1 گیگ | 330,000",
                        "callback_data" => "v2_1"
                    ]
                ],

                [
                    [
                        "text" => "📦 5 گیگ | 1,500,000",
                        "callback_data" => "v2_5"
                    ]
                ],

                [
                    [
                        "text" => "📦 10 گیگ | 2,800,000",
                        "callback_data" => "v2_10"
                    ]
                ]

            ]
        ];

        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "پلن موردنظر V2rayNG را انتخاب کنید:",
            "reply_markup" => json_encode($keyboard)
        ]);
    }

    # دریافت رسید
    elseif(isset($message["photo"])){

        if(!isset($users[$chat_id])){

            bot("sendMessage", [
                "chat_id" => $chat_id,
                "text" => "ابتدا سرویس انتخاب کنید."
            ]);

            exit;
        }

        $service = $users[$chat_id];

        $photo = end($message["photo"])["file_id"];

        $caption = "
🧾 رسید جدید

👤 کاربر:
$first_name

🆔 آیدی:
$chat_id

📦 پلن:
$service
";

        $keyboard = [
            "inline_keyboard" => [
                [
                    [
                        "text" => "✅ تایید پرداخت",
                        "callback_data" => "ok_$chat_id"
                    ]
                ]
            ]
        ];

        bot("sendPhoto", [
            "chat_id" => $admin_id,
            "photo" => $photo,
            "caption" => $caption,
            "reply_markup" => json_encode($keyboard)
        ]);

        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "✅ رسید ارسال شد\nبعد از تایید ادمین نتیجه اعلام می‌شود."
        ]);
    }
}

# دکمه ها
if(isset($update["callback_query"])){

    $call = $update["callback_query"];

    $data = $call["data"];

    # انتخاب پلن
    if(
        $data == "ovpn_5" ||
        $data == "ovpn_10" ||
        $data == "v2_1" ||
        $data == "v2_5" ||
        $data == "v2_10"
    ){

        $plans = [

            "ovpn_5" => "OpenVPN | 5 گیگ",
            "ovpn_10" => "OpenVPN | 10 گیگ",

            "v2_1" => "V2rayNG | 1 گیگ",
            "v2_5" => "V2rayNG | 5 گیگ",
            "v2_10" => "V2rayNG | 10 گیگ"

        ];

        $user_id = $call["from"]["id"];

        $users[$user_id] = $plans[$data];

        file_put_contents($file, json_encode($users));

        bot("sendMessage", [
            "chat_id" => $user_id,
            "text" => "
✅ پلن انتخاب شد:

{$plans[$data]}

💳 شماره کارت:

6219861864812622

👤 رضا پروانه پور

━━━━━━━━━━━

بعد از پرداخت رسید را ارسال کنید.
"
        ]);
    }

    # تایید پرداخت
    elseif(strpos($data, "ok_") === 0){

        $user_id = str_replace("ok_", "", $data);

        bot("sendMessage", [
            "chat_id" => $user_id,
            "text" => "
✅ پرداخت شما تایید شد

📦 سرویس شما به‌زودی ارسال می‌شود.

سپاس از خرید شما ❤️
"
        ]);

        bot("answerCallbackQuery", [
            "callback_query_id" => $call["id"],
            "text" => "پرداخت تایید شد"
        ]);
    }
}

?>
