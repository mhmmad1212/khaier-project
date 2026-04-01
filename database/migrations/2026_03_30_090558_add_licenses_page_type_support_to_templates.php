<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        //
        // لا نحتاج تعديل بنية page_templates هنا إذا كان page_type عندكم string/varchar
        // هذه migration حجز فقط لتوثيق الإضافة الجديدة منطقيًا.
        //
    }

    public function down(): void
    {
        //
    }
};
