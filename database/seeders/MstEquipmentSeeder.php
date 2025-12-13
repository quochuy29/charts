<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstEquipmentSeeder extends Seeder
{
    private int $order = 1;

    private function node(
        string $code,
        string $name,
        int $level,
        int $parentId,
        int $itemType
    ): int {
        return DB::table('mst_equipment')->insertGetId([
            'code' => $code,
            'name' => $name,
            'level' => $level,
            'parent_id' => $parentId,
            'item_type' => $itemType,
            'graph_type' => 5,
            'calculation_formula' => null,
            'display_order' => $this->order++,
            'is_visibility' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function run(): void
    {
        DB::table('mst_equipment')->truncate();

        /** =====================
         * LINE 1*/
        $L1 = $this->node('L1', '1ライン', 1, 0, 1);

        /** ---- 工場 ---- */
        $L1_FAC = $this->node('L1_FAC', '工場', 2, $L1, 2);

        $this->utility($L1_FAC, 'L1_FAC_ELEC', '電気', [
            ['L11_E', '電気'],
            ['L12_REF', '冷凍機'],
            ['L13_HP', 'HP'],
        ]);

        $this->utility($L1_FAC, 'L1_FAC_GAS', 'ガス', [
            ['L1_GAS', 'ガス'],
        ]);

        $this->utility($L1_FAC, 'L1_FAC_STEAM', '蒸気', [
            ['L1_STEAM', '蒸気'],
        ]);

        $this->utility($L1_FAC, 'L1_FAC_AIR', 'エアー', [
            ['L1_AIR_H', '高圧-ドライ'],
            ['L1_AIR_LD', '低圧-ドライ'],
            ['L1_AIR_L', '低圧'],
        ]);

        $this->utility($L1_FAC, 'L1_FAC_WATER', '水', [
            ['L1_W_IND', '工水'],
            ['L1_W_COLD', '冷水'],
            ['L1_W_PURE', '純水'],
        ]);

        $this->node('L1_TEMP', '外気温', 3, $L1_FAC, 3);
        $this->node('L1_PROD', '生産台数', 3, $L1_FAC, 3);

        /** ---- ブース ---- */
        $L1_BOOTH = $this->node('L1_BOOTH', 'ブース', 2, $L1, 2);

        $this->utility($L1_BOOTH, 'L1_BOOTH_E', '電気', [
            ['B1_E_INTER', '中塗りブース'],
            ['B1_E_D_BASE', '上塗りDベース'],
            ['B1_E_D_PH', '上塗りDPH'],
            ['B1_E_D_CLR', '上塗りDクリア'],
            ['B1_E_E_BASE', '上塗りEベース'],
            ['B1_E_E_PH', '上塗りEPH'],
            ['B1_E_E_CLR', '上塗りEクリア'],
        ]);

        $this->utility($L1_BOOTH, 'L1_BOOTH_G', 'ガス', [
            ['B1_G_INTER', '中塗りブース'],
            ['B1_G_D_BASE', '上塗りDベース'],
            ['B1_G_D_PH', '上塗りDPH'],
            ['B1_G_D_CLR', '上塗りDクリア'],
            ['B1_G_E_BASE', '上塗りEベース'],
            ['B1_G_E_PH', '上塗りEPH'],
            ['B1_G_E_CLR', '上塗りEクリア'],
        ]);

        /** ---- 乾燥炉 ---- */
        $L1_OVEN = $this->node('L1_OVEN', '乾燥炉', 2, $L1, 2);

        $this->utility($L1_OVEN, 'L1_OVEN_E', '電気', [
            ['O1_E_ED', '電着乾燥炉'],
            ['O1_E_INTER', '中塗り乾燥炉'],
            ['O1_E_TOPD', '上塗りD乾燥炉'],
            ['O1_E_TOPE', '上塗りE乾燥炉'],
            ['O1_E_DEO', '電着RTO'],
            ['O1_E_RTO', '中上塗RTO'],
        ]);

        $this->utility($L1_OVEN, 'L1_OVEN_G', 'ガス', [
            ['O1_G_ED', '電着乾燥炉'],
            ['O1_G_INTER', '中塗り乾燥炉'],
            ['O1_G_TOPD', '上塗りD乾燥炉'],
            ['O1_G_TOPE', '上塗りE乾燥炉'],
            ['O1_G_DEO', '電着RTO'],
            ['O1_G_RTO', '中上塗RTO'],
        ]);

        /** ---- A変台 ---- */
        $SUBA = $this->node('L1_SUBA', 'A変台', 2, $L1, 2);
        $this->utility($SUBA, 'L1_SUBA_E', '電気', [
            ['SUBA_1', '1バンク'],
            ['SUBA_2', '2バンク'],
            ['SUBA_3', '3バンク'],
            ['SUBA_4', '4バンク'],
            ['SUBA_5', '5バンク'],
            ['SUBA_6', '6バンク'],
            ['SUBA_7', '7バンク'],
            ['SUBA_8', '8バンク'],
        ]);

        /** ---- B変台 ---- */
        $SUBB = $this->node('L1_SUBB', 'B変台', 2, $L1, 2);
        $this->utility($SUBB, 'L1_SUBB_E', '電気', [
            ['SUBB_1', '1バンク'],
            ['SUBB_2', '2バンク'],
            ['SUBB_3', '3バンク'],
            ['SUBB_4', '4バンク'],
            ['SUBB_5', '5バンク'],
            ['SUBB_6', '6バンク'],
            ['SUBB_7', '7バンク'],
            ['SUBB_8', '8バンク'],
        ]);

        /** ---- 冷凍機 ---- */
        $FZ = $this->node('L1_FZ', '冷凍機', 2, $L1, 2);
        $this->utility($FZ, 'L1_FZ_E', '電気', [
            ['FZ_TOTAL', '冷凍機合計'],
            ['FZ_R301', 'R301冷凍機'],
            ['FZ_R302', 'R302冷凍機'],
            ['FZ_R303', 'R303冷凍機'],
            ['FZ_R304', 'R304冷凍機'],
            ['FZ_R305', 'R305冷凍機'],
        ]);

        /** ---- HP ---- */
        $HP = $this->node('L1_HP', 'HP', 2, $L1, 2);
        $this->utility($HP, 'L1_HP_E', '電気', [
            ['HP_1', 'HP'],
        ]);

        /** =====================
         * LINE 2*/
        $L2 = $this->node('L2', '2ライン', 1, 0, 1);

        $L2_FAC = $this->node('L2_FAC', '工場', 2, $L2, 2);

        $this->utility($L2_FAC, 'L2_FAC_E', '電気', [
            ['L2_E', '電気'],
            ['L2_REF', '冷凍機'],
            ['L2_HP', 'HP'],
        ]);

        $this->utility($L2_FAC, 'L2_FAC_G', 'ガス', [['L2_GAS', 'ガス']]);
        $this->utility($L2_FAC, 'L2_FAC_S', '蒸気', [['L2_STEAM', '蒸気']]);

        $this->utility($L2_FAC, 'L2_FAC_A', 'エアー', [
            ['L2_A_H', '高圧(ドライ)'],
            ['L2_A_LD', '低圧(ドライ)'],
            ['L2_A_L', '低圧'],
        ]);

        $this->utility($L2_FAC, 'L2_FAC_W', '水', [
            ['L2_W_IND', '工水'],
            ['L2_W_COLD', '冷水'],
            ['L2_W_PURE', '純水'],
        ]);

        $this->node('L2_TEMP', '外気温', 3, $L2_FAC, 3);
        $this->node('L2_PROD', '生産台数', 3, $L2_FAC, 3);
    }

    private function utility(int $facilityId, string $code, string $name, array $items): void
    {
        $utilityId = $this->node($code, $name, 3, $facilityId, 3);

        foreach ($items as [$c, $n]) {
            $this->node($c, $n, 4, $utilityId, 4);
        }
    }
}
