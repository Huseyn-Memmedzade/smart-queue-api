# Smart Queue API (Növbə İdarəetmə Sistemi)

Kiçik xidmət mərkəzləri üçün nəzərdə tutulmuş sadə, etibarlı və yüksək performanslı **Növbə İdarəetmə REST API** sistemi.

Bu layihə intern qəbul tapşırığı (interview task) üçün **Core PHP** və **MySQL** istifadə olunaraq, heç bir kənar freymvorkdan (Laravel və s.) istifadə edilmədən sıfırdan hazırlanmışdır.

---

## 🛠 İstifadə Olunan Texnologiyalar

- **Backend:** Core PHP (PDO ilə)
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, Vanilla JavaScript (Fetch API)

---

## 🚀 API Endpoint-ləri

| Metod | Endpoint | Təsvir |
| :--- | :--- | :--- |
| `POST` | `/api/queue.php` | Yeni müştərini növbəyə əlavə edir |
| `GET` | `/api/queue.php` | Bütün gözləyən (`Waiting`) müştəriləri göstərir |
| `GET` | `/api/queue.php?id={id}` | Müştəri məlumatını və növbədə neçənci olduğunu göstərir |
| `POST` | `/api/next.php` | Növbədəki növbəti müştərini çağırır |
| `DELETE` | `/api/queue.php?id={id}` | Müştərini növbədən silir |

---

## 💡 `/next` Endpoint-i Üçün Həll (Race Condition / Parallel Sorğular)

### Problem:
Eyni anda iki və ya daha çox operator `/next` düyməsini sıxarsa, sistemdə **Race Condition** baş verə bilər və eyni müştəri iki dəfə çağırıla bilər.

### Həll Üsulu (Pessimistic Locking):
Bu problemin qarşısını almaq üçün baza səviyyəsində kilidləmə mexanizmindən istifadə olunmuşdur:

1. `api/next.php` daxilində **MySQL Transaction** (`$pdo->beginTransaction()`) başladılır.
2. `SELECT ... FOR UPDATE` sorğusu vasitəsilə növbədə olan ilk `Waiting` statuslu müştəri seçilir. Bu əməliyyat həmin sətiri baza səviyyəsində **kilidləyir (Lock edir)**.
3. Paralel gələn ikinci sorğu birinci transaction bitənə (`commit` və ya `rollBack` olana) qədər gözləmə rejimində qalır.
4. Birinci sorğu müştərinin statusunu `Serving` etdikdən sonra kilid açılır və ikinci sorğu növbəti müştərini emal edir.

Bu yanaşma hər bir müştərinin dəqiq olaraq yalnız **bir dəfə** çağırılacağını zəmanət altına alır.

---

## 🗄️ Verilənlər Bazası Strukturu (`schema.sql`)

```sql
CREATE DATABASE IF NOT EXISTS queue_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE queue_db;

CREATE TABLE IF NOT EXISTS queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    status ENUM('Waiting', 'Serving', 'Completed') DEFAULT 'Waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
