import telebot
from telebot import types

TOKEN = "8698840173:AAHNRrDPvEWW0XuDfyM_tlNXSBBw_flL158"

bot = telebot.TeleBot(TOKEN)

@bot.message_handler(commands=['start'])
def start(message):
markup = types.ReplyKeyboardMarkup(resize_keyboard=True)

```
btn1 = types.KeyboardButton("🟢 خرید OPENVPN")
btn2 = types.KeyboardButton("🚀 خرید V2rayNG")
btn3 = types.KeyboardButton("📞 پشتیبانی")

markup.add(btn1)
markup.add(btn2)
markup.add(btn3)

bot.send_message(
    message.chat.id,
    "👋 به BLACK SHOP خوش اومدی",
    reply_markup=markup
)
```

@bot.message_handler(func=lambda message: True)
def menu(message):

```
if message.text == "🟢 خرید OPENVPN":
    bot.send_message(
        message.chat.id,
        "📦 پلن های OPENVPN\n\n5 گیگ — 1,800,000\n10 گیگ — 3,000,000"
    )

elif message.text == "🚀 خرید V2rayNG":
    bot.send_message(
        message.chat.id,
        "🚀 تعرفه V2rayNG\n\n1 گیگ — 330\n5 گیگ — 1500\n10 گیگ — 2800"
    )

elif message.text == "📞 پشتیبانی":
    bot.send_message(
        message.chat.id,
        "@black_shop"
    )
```

bot.infinity_polling()
