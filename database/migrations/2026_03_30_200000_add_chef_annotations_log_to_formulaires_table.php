<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formulaires', function (Blueprint $table) {
            $table->json('chef_annotations_log')->nullable()->after('annotation_chef');
        });

        if (Schema::hasColumn('formulaires', 'annotation_chef')) {
            foreach (DB::table('formulaires')
                ->whereNotNull('annotation_chef')
                ->where('annotation_chef', '!=', '')
                ->cursor() as $row) {
                $log = [[
                    'service_code' => $row->service_code,
                    'user_id' => null,
                    'kind' => 'note',
                    'body' => $row->annotation_chef,
                    'created_at' => $row->updated_at ?? now()->toIso8601String(),
                ]];
                DB::table('formulaires')->where('id', $row->id)->update([
                    'chef_annotations_log' => json_encode($log),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('formulaires', function (Blueprint $table) {
            $table->dropColumn('chef_annotations_log');
        });
    }
};
