document.addEventListener('DOMContentLoaded', function () {
    const els = {
        loainha: document.getElementById('calc-loainha'),
        logioi: document.getElementById('calc-logioi'),
        rongtret: document.getElementById('calc-rongtret'),
        daitret: document.getElementById('calc-daitret'),
        solau: document.getElementById('calc-solau'),
        loaimong: document.getElementById('calc-loaimong'),
        loaimai: document.getElementById('calc-loaimai'),
        goi: document.querySelectorAll('input[name="calc-goi"]'),

        breakdown: document.getElementById('calc-breakdown'),
        totalArea: document.getElementById('res-total-area'),
        unitPrice: document.getElementById('res-unit-price'),
        hemFeeRow: document.getElementById('res-hem-fee-row'),
        hemFee: document.getElementById('res-hem-fee'),
        totalCost: document.getElementById('res-total-cost')
    };

    // Prevent errors if not on calculator page
    if (!els.loainha) return;

    function formatVND(num) {
        return num.toLocaleString('vi-VN');
    }

    function calculate() {
        // 1. Get Values
        const rong = parseFloat(els.rongtret.value) || 0;
        const dai = parseFloat(els.daitret.value) || 0;
        const logioi = parseFloat(els.logioi.value) || 0;
        const solau = parseInt(els.solau.value) || 0;
        const loaimong = els.loaimong.value;
        const loaimai = els.loaimai.value;
        const loainha = els.loainha.value;

        let goi_selected = 'tho';
        els.goi.forEach(r => { if (r.checked) goi_selected = r.value; });

        // 2. Base Area
        const dt_tret = Math.round(rong * dai * 100) / 100;

        // 3. Coefficients
        let heso_mong = 0.5; // coc
        if (loaimong === 'don') heso_mong = 0.4;
        if (loaimong === 'bang') heso_mong = 0.7;

        let heso_mai = 0.5; // btct
        if (loaimai === 'tole') heso_mai = 0.3;
        if (loaimai === 'ngoi') heso_mai = 0.7;
        if (loaimai === 'ngoibt') heso_mai = 1.0;

        let heso_bancong = 0; // chieu vuon ra cua ban cong
        if (logioi >= 7 && logioi < 12) heso_bancong = 0.9;
        if (logioi >= 12 && logioi < 20) heso_bancong = 1.2;
        if (logioi >= 20) heso_bancong = 1.4;

        // 4. Calculate Parts
        const dt_mong = Math.round(dt_tret * heso_mong * 100) / 100;
        const dt_lau = Math.round(dt_tret * solau * 100) / 100;

        // Ban cong chi tinh cho cac lau (tret k co ban cong)
        let dt_bancong_1_lau = Math.round(rong * heso_bancong * 100) / 100;
        const dt_bancong = Math.round(dt_bancong_1_lau * solau * 100) / 100;

        const dt_mai = Math.round(dt_tret * heso_mai * 100) / 100;

        let tong_dt = dt_mong + dt_tret + dt_lau + dt_bancong + dt_mai;
        tong_dt = Math.round(tong_dt * 100) / 100;

        // 5. Unit Price (Tiered Pricing based on dt_tret)
        const dt_san = dt_tret; // Using ground floor area for tiers
        let dg_base = 0;

        const tiers = {
            nhapho: {
                tho: [
                    { max: 40, price: 3700000 },
                    { max: 50, price: 3600000 },
                    { max: 60, price: 3500000 },
                    { max: 80, price: 3400000 },
                    { max: Infinity, price: 3400000 }
                ],
                trongoi: [
                    { max: 40, price: 7695300 },
                    { max: 50, price: 6723000 },
                    { max: 60, price: 5930000 },
                    { max: 80, price: 5300000 },
                    { max: Infinity, price: 5100000 }
                ]
            },
            bietthu: {
                tho: [
                    { max: 40, price: 3900000 },
                    { max: 50, price: 3800000 },
                    { max: 60, price: 3700000 },
                    { max: 80, price: 3600000 },
                    { max: Infinity, price: 3400000 }
                ],
                trongoi: [
                    { max: 40, price: 8493900 },
                    { max: 50, price: 7449000 },
                    { max: 60, price: 6590000 },
                    { max: 80, price: 5900000 },
                    { max: Infinity, price: 5700000 }
                ]
            }
        };

        const activeTiers = tiers[loainha][goi_selected];
        for (let i = 0; i < activeTiers.length; i++) {
            if (dt_san < activeTiers[i].max) {
                dg_base = activeTiers[i].price;
                break;
            }
        }

        // If the area is exactly equal to the boundary, or larger than 80, it falls into the last tier (Infinity).
        if (dg_base === 0) dg_base = activeTiers[activeTiers.length - 1].price;

        // Phu phi hem
        let phu_phi_hem = 0;
        if (logioi <= 3) phu_phi_hem = 150000;
        else if (logioi > 3 && logioi < 5) phu_phi_hem = 75000;

        const dg_final = dg_base + phu_phi_hem;
        const tong_tien = Math.round(tong_dt * dg_final);

        // 6. Render UI
        // Render breakdown
        let breakdownHTML = '';
        const addBreakdownRow = (label, area, pct) => {
            breakdownHTML += `
                <div class="flex justify-between items-center group/line border-b border-dashed border-gray-200 dark:border-white/10 pb-1">
                    <span class="text-gray-600 dark:text-gray-300 text-xs">${label} <span class="text-[10px] bg-gray-200/60 dark:bg-white/10 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded ml-1">${pct}%</span></span>
                    <span class="font-bold text-sm text-gray-900 dark:text-white">${area} m²</span>
                </div>
            `;
        };

        addBreakdownRow('Móng', dt_mong, heso_mong * 100);
        addBreakdownRow('Tầng Trệt', dt_tret, 100);
        if (solau > 0) {
            addBreakdownRow(`Lầu (x${solau})`, dt_lau, 100);
            if (dt_bancong > 0) {
                addBreakdownRow('Ban công', dt_bancong, 100);
            }
        }
        addBreakdownRow('Mái', dt_mai, heso_mai * 100);

        els.breakdown.innerHTML = breakdownHTML;

        // Render Totals
        els.totalArea.innerText = formatVND(tong_dt);
        els.unitPrice.innerText = formatVND(dg_base);

        if (phu_phi_hem > 0) {
            els.hemFeeRow.style.display = 'flex';
            els.hemFee.innerText = '+' + formatVND(phu_phi_hem);
        } else {
            els.hemFeeRow.style.display = 'none';
        }

        els.totalCost.innerText = formatVND(tong_tien);
    }

    // Bind events
    const inputs = [
        els.loainha, els.logioi, els.rongtret, els.daitret,
        els.solau, els.loaimong, els.loaimai
    ];

    inputs.forEach(input => {
        if (input) input.addEventListener('input', calculate);
    });

    els.goi.forEach(radio => {
        radio.addEventListener('change', calculate);
    });

    // Init
    calculate();
});
