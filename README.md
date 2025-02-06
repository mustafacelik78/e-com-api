# Sipariş Yönetimi ve İndirim API'si

Bu proje, **Laravel 11** ve **Docker** kullanarak geliştirilmiş bir **Sipariş Yönetimi ve Dinamik İndirim Hesaplama API'sidir**.
Sistem, **RESTful API** standartlarına uygun çalışmakta olup, sipariş yönetimi, dinamik indirim hesaplama gibi işlemleri desteklemektedir.

---

## 🚀 Özellikler

- **Laravel 11** tabanlı geliştirilmiştir
- **Docker** ile kolay kurulum ve deployment
- **Sanctum** ile token bazlı kimlik doğrulama
- **Dinamik indirim hesaplama** sistemi
- **RESTful API** prensiplerine uygun geliştirilmiştir
- **Swagger** ile API dokümentasyonu
- **MySQL** veritabanı kullanımı

---

## 📦 Kurulum

### 🐳 Docker ile Kurulum

Projeyi Docker ile kolayca ayağa kaldırabilirsiniz:

```bash
git clone https://github.com/mustafacelik78/e-com-api.git
cd e-com-api
cp .env.example .env
docker-compose up -d --build
```

Ardından, veritabanını oluşturmak için:

```bash
docker-compose exec laravel_app php artisan migrate:fresh --seed
```

---

## 🔐 API Kullanımı

### ✅ Kimlik Doğrulama

**Kullanıcı kayıt olmak için:**
```http
POST /api/register
```

**Giriş yapmak için:**
```http
POST /api/login
```

### 🛒 Sipariş Oluşturma
```http
POST /api/orders
Authorization: Bearer {token}
```

### 🎯 İndirim Hesaplama
```http
POST /api/discounts/apply
Authorization: Bearer {token}
```

---

## 📖 API Dokümantasyonu

API'nin detaylı dokümantasyonuna aşağıdaki bağlantıdan ulaşabilirsiniz:

🔗 [Swagger Dokümantasyonu](http://localhost:8080/api/documentation)

---

## 🖥️ PhpMyAdmin

PhpMyAdmin'e aşağıdaki bağlantıdan erişebilirsiniz:

🔗 [PhpMyAdmin](http://localhost:8081/)

---

## 📊 İndirim Kuralları

İndirimler için **dinamik bir yapı** oluşturuldu. `discount_rules` tablosunda yer alan **type** alanına göre indirimin türü belirlenmektedir.

| Type | Açıklama |
|------|---------|
| **PERCENTAGE** | Belirli bir tutar üzerindeki siparişlere yüzdesel indirim uygular. |
| **BUY_X_GET_Y_FREE** | Belirtilen kategoride belirli miktarda ürün alana ekstra ücretsiz ürün sağlar. |
| **CATEGORY_PERCENTAGE** | Belirli bir kategori için minimum ürün sayısına ulaşanlara yüzdesel indirim uygular. |
| **LIMITED_USE** | Müşteri bazlı sınırlı kullanım indirimleri uygular. |

**Örnek Kullanımlar:**

📌 **Yüzdesel İndirim (PERCENTAGE)**
```json
{
  "type": "PERCENTAGE",
  "value": 10, // %10 indirim sağlar
  "conditions": {
    "min_total": 1000, // 1000 tl üzerine
    "customer_limit": 10 // müşteri bu indirimden 10 kez yararlanabilir
  }
}
```
🛍️ **Belirli Ürün Sayısına Göre İndirim (BUY_X_GET_Y_FREE)**
```json
{
  "type": "BUY_X_GET_Y_FREE",
  "conditions": {
    "x": 6, // belirlenen kategoride x adet alana y adeti indirim olarak uygulanır
    "y": 1,
    "category_id": 2,
    "customer_limit": 10 // müşteri bu indirimden 10 kez yararlanabilir
  }
}
```
🏷️ **Kategori Bazlı Yüzdesel İndirim (CATEGORY_PERCENTAGE)**
```json
{
  "type": "CATEGORY_PERCENTAGE",
  "value": 20, // %20 indirim uygulanır
  "conditions": {
    "sort": "asc", // indirimin en ucuz ürüne mi yoksa en pahalı ürüne mi yapılacağını belirler. asc: ucuz olana, desc: pahalı olana indirim uygular
    "category_id": 1, // belirli kategorilerde indirim uygulanır
    "min_quantity": 2, // belirli miktarda indirim uygulanır
    "customer_limit": 10 // müşteri bu indirimden 10 kez yararlanabilir
  }
}
```
🎟️ **Müşteri Bazlı Sınırlı İndirim (LIMITED_USE)**
```json
{
  "type": "LIMITED_USE", // her müşteriye uygulanır
  "value": 50, // 50 birim indirim yapılır
  "conditions": {
    "customer_limit": 1 // müşteri bu indirimden 1 kez yararlanabilir
  }
}
```
