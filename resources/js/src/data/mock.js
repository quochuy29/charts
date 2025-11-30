// src/data/mockData.js

export const initialTreeData = [
    {
        id: "factory-1",
        name: "Factory 1 - Main Production",
        type: "factory",
        children: [
            {
                id: "line-1",
                name: "Line 1 - Assembly",
                type: "line",
                children: [
                    {
                        id: "shop-1",
                        name: "Shop A - Welding",
                        type: "shop",
                        children: [
                            { id: "eq-1", name: "Welder Unit 01", type: "equipment" },
                            { id: "eq-2", name: "Welder Unit 02", type: "equipment" },
                            { id: "eq-3", name: "Transformer T-001", type: "equipment" }
                        ]
                    },
                    {
                        id: "shop-2",
                        name: "Shop B - Painting",
                        type: "shop",
                        children: [
                            { id: "eq-4", name: "Paint Booth 01", type: "equipment" },
                            { id: "eq-5", name: "Paint Booth 02", type: "equipment" },
                        ]
                    },
                ],
            },
        ],
    },
    {
        id: "factory-2",
        name: "Factory 2 - Processing",
        type: "factory",
        children: [],
    },
];

// --- HỆ SỐ CHUYỂN ĐỔI ĐƠN VỊ ---
const UNIT_MULTIPLIERS = {
    energy: 1,      // kWh (Gốc)
    cost: 0.15,     // USD (Giả sử 0.15$ / kWh)
    co2: 0.4        // kg (Giả sử 0.4kg CO2 / kWh)
};

// --- HELPER: DATA DAILY ---
function generateDailyNodeData(scaleFactor = 1, dayFactor = 1, multiplier = 1) {
    const labels = [];
    const values = [];
    for (let hour = 5; hour <= 29; hour++) {
        const displayHour = hour > 24 ? hour - 24 : hour;
        const timeLabel = `${displayHour.toString().padStart(2, "0")}:00`;

        // Logic: Base * Scale Node * Scale Ngày * Unit Multiplier
        const baseValue = 150 + Math.random() * 100;
        const scaledValue = parseFloat((baseValue * scaleFactor * dayFactor * multiplier).toFixed(2));

        labels.push(timeLabel);
        values.push(scaledValue);
    }
    return { labels, values };
}

// --- MAIN GENERATOR FUNCTION ---
// Cập nhật: Thêm tham số unitType
export function generateChartDataByNode(nodeId, viewMode, selectedDate, unitType = 'energy') {
    const isEquipment = nodeId.startsWith('eq-');
    const isShop = nodeId.startsWith('shop-');
    const isLine = nodeId.startsWith('line-');
    const isFactory = nodeId.startsWith('factory-');

    // Lấy hệ số nhân dựa trên Unit Type
    const multiplier = UNIT_MULTIPLIERS[unitType] || 1;

    // Hệ số ngày (Chẵn/Lẻ)
    let dayFactor = 1;
    if (selectedDate) {
        const day = selectedDate.getDate();
        dayFactor = day % 2 === 0 ? 1.2 : 0.8;
    }

    // Tỷ lệ Scale Node
    let scaleFactor = 1;
    if (isLine) scaleFactor = 0.5;
    else if (isShop) scaleFactor = 0.2;
    else if (isEquipment) scaleFactor = 0.05;

    // --- DAILY DATA ---
    if (viewMode === 'daily') {
        const { labels, values: nodeConsumptionData } = generateDailyNodeData(scaleFactor, dayFactor, multiplier);
        // Factory Average cũng phải nhân với Multiplier
        const factoryTotalData = Array(25).fill(200 * dayFactor * multiplier);
        return { labels, nodeConsumptionData, factoryTotalData };
    }

    // --- COMPARISON DATA ---
    if (viewMode === 'comparison') {
        const { labels, values: period1Data } = generateDailyNodeData(scaleFactor, dayFactor, multiplier);
        const period2Data = Array(25).fill(180 * scaleFactor * multiplier);
        return { labels, period1Data, period2Data };
    }

    // --- PERIOD DATA ---
    if (viewMode === 'period') {
        const labels = ["Day 1", "Day 2", "Day 3", "Day 4", "Day 5", "Day 6", "Day 7"];
        const randomShift = dayFactor === 1.2 ? 500 : 0;

        // Nhân multiplier vào giá trị gốc
        const factoryTotalData = Array(7).fill((3500 + randomShift) * multiplier);
        const nodeConsumptionData = factoryTotalData.map(val =>
            parseFloat((val * scaleFactor + (Math.random() * 500 - 250) * multiplier).toFixed(0))
        );
        return { labels, nodeConsumptionData, factoryTotalData };
    }

    // --- SHOP COMPARISON ---
    if (viewMode === 'shop-comparison') {
        if (isFactory || isLine) {
            const labels = ["Shop A - Welding", "Shop B - Painting"];
            const base = 250 * dayFactor * multiplier;
            return {
                labels,
                actualData: labels.map((_, i) => base + Math.random() * 100 * multiplier),
                targetData: labels.map((_) => base * 1.1),
            };
        }
        return { labels: ['N/A'], datasets: [] };
    }

    return { labels: [], datasets: [] };
}