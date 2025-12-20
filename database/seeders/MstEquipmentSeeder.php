<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MstEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset bảng
        Schema::disableForeignKeyConstraints();
        DB::table('mst_equipment')->truncate();
        Schema::enableForeignKeyConstraints();

        // Dữ liệu dựa trên cấu trúc text mới nhất
        $treeData = [
            [
                'name' => '1ライン', // Level 1
                'children' => [
                    // --- Nhóm Level 2: 工場 ---
                    [
                        'name' => '工場',
                        'children' => [
                            [
                                'name' => '電気', // Level 3
                                'children' => ['電気', '冷凍機', 'HP'] // Level 4
                            ],
                            [
                                'name' => 'ガス',
                                'children' => ['ガス']
                            ],
                            [
                                'name' => '蒸気',
                                'children' => ['蒸気']
                            ],
                            [
                                'name' => 'エアー',
                                'children' => ['高圧(ﾄﾞﾗｲ)', '低圧(ﾄﾞﾗｲ)', '低圧']
                            ],
                            [
                                'name' => '水',
                                'children' => ['工水', '冷水', '純水']
                            ],
                            [
                                'name' => '工場',
                                'children' => ['外気温', '生産台数']
                            ],
                        ]
                    ],
                    // --- Nhóm Level 2: 電気 ---
                    [
                        'name' => '電気',
                        'children' => [
                            [
                                'name' => 'プース', // Level 3 (Giữ nguyên text gốc là プース)
                                'children' => ['中塗りブース', '上塗りAベース', '上塗りAPH', '上塗りAクリア', '上塗りBベース', '上塗りBPH', '上塗りBクリア']
                            ],
                            [
                                'name' => '乾燥炉',
                                'children' => ['電着乾燥炉', '中塗り乾燥炉', '上塗りA乾燥炉', '上塗りB乾燥炉', '電着脱臭炉', '中上塗RTO']
                            ],
                            [
                                'name' => '変台',
                                'children' => [
                                    'A変台1バンク', 'A変台2バンク', 'A変台3バンク', 'A変台4バンク', 'A変台5バンク', 'A変台6バンク', 'A変台7バンク',
                                    'B変台1バンク', 'B変台2バンク', 'B変台3バンク', 'B変台4バンク', 'B変台5バンク', 'B変台6バンク', 'B変台7バンク', 'B変台8バンク'
                                ]
                            ],
                            [
                                'name' => '冷凍機',
                                'children' => ['冷凍機合計', 'R301冷凍機', 'R302冷凍機', 'R303冷凍機', 'R304冷凍機', 'R305冷凍機']
                            ],
                            [
                                'name' => 'HP',
                                'children' => ['HP']
                            ]
                        ]
                    ],
                    // --- Nhóm Level 2: ガス ---
                    [
                        'name' => 'ガス',
                        'children' => [
                            [
                                'name' => 'ブース',
                                'children' => ['中塗りブース', '上塗りAベース', '上塗りAPH', '上塗りAクリア', '上塗りBベース', '６バンク', '上塗りBPH', '上塗りBクリア']
                            ],
                            [
                                'name' => '乾燥炉',
                                'children' => ['電着乾燥炉', '中塗り乾燥炉', '上塗りA乾燥炉', '上塗りB乾燥炉', '電着脱臭炉', '中上塗RTO']
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => '２ライン', // Level 1 (Lưu ý: số ２ là full-width theo input)
                'children' => [
                    // --- Nhóm Level 2: 工場 ---
                    [
                        'name' => '工場',
                        'children' => [
                            [
                                'name' => '電気',
                                'children' => ['電気', '冷凍機', 'HP']
                            ],
                            [
                                'name' => 'ガス',
                                'children' => ['ガス']
                            ],
                            [
                                'name' => '蒸気',
                                'children' => ['蒸気']
                            ],
                            [
                                'name' => 'エアー',
                                'children' => ['高圧(ﾄﾞﾗｲ)', '低圧(ﾄﾞﾗｲ)', '低圧']
                            ],
                            [
                                'name' => '水',
                                'children' => ['工水', '冷水', '純水']
                            ],
                            [
                                'name' => '工場',
                                'children' => ['外気温', '生産台数']
                            ],
                        ]
                    ],
                    // --- Nhóm Level 2: 電気 ---
                    [
                        'name' => '電気',
                        'children' => [
                            [
                                'name' => 'プース', // Level 3 (Giữ nguyên text gốc là プース)
                                'children' => ['中塗りブース', '上塗りDベース', '上塗りDPH', '上塗りDクリア', '上塗りEベース', '上塗りEPH', '上塗りEクリア']
                            ],
                            [
                                'name' => '乾燥炉',
                                'children' => ['電着乾燥炉', '中塗り乾燥炉', '上塗りD乾燥炉', '上塗りE乾燥炉', '電着RTO', '中上塗RTO']
                            ],
                            [
                                'name' => '変台',
                                'children' => [
                                    'C変台1バンク', 'C変台2バンク', 'C変台3バンク', 'C変台4バンク', 'C変台5バンク',
                                    'D変台1バンク', 'D変台2バンク', 'D変台3バンク', 'D変台4バンク',
                                    'E変台1バンク'
                                ]
                            ]
                        ]
                    ],
                    // --- Nhóm Level 2: ガス ---
                    [
                        'name' => 'ガス',
                        'children' => [
                            [
                                'name' => 'ブース',
                                'children' => ['中塗りブース', '上塗りDベース', '上塗りDPH', '上塗りDクリア', '上塗りEベース', '上塗りEPH', '上塗りEクリア']
                            ],
                            [
                                'name' => '乾燥炉',
                                'children' => ['電着乾燥炉', '中塗り乾燥炉', '上塗りD乾燥炉', '上塗りE乾燥炉', '電着RTO', '中上塗RTO']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Bắt đầu insert
        $this->insertNode($treeData, null, 1);
    }

    /**
     * Hàm đệ quy insert node
     * Logic: item_type = level (1: Line, 2: Utility, 3: Type, 4: Equipment)
     */
    private function insertNode($nodes, $parentId, $level)
    {
        $displayOrder = 1;

        foreach ($nodes as $node) {
            // Xác định tên và children
            if (is_array($node)) {
                $name = $node['name'];
                $children = $node['children'] ?? [];
            } else {
                // Trường hợp node lá (Level 4 - Equipment)
                $name = $node;
                $children = [];
            }

            $code = strtoupper(uniqid());

            $id = DB::table('mst_equipment')->insertGetId([
                'code'          => $code,
                'name'          => $name,
                'level'         => $level,
                'parent_id'     => $parentId,
                'item_type'     => $level, // item_type bằng đúng giá trị level
                'display_order' => $displayOrder++,
                'is_visibility' => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // Đệ quy
            if (!empty($children)) {
                $this->insertNode($children, $id, $level + 1);
            }
        }
    }
}