const puppeteer = require('puppeteer');
const fs = require('fs');

(async () => {
    const outDir = './screenshots';
    if (!fs.existsSync(outDir)) {
        fs.mkdirSync(outDir);
    }

    const browser = await puppeteer.launch({ headless: 'new', args: ['--no-sandbox'] });
    const page = await browser.newPage();
    await page.setViewport({ width: 1366, height: 768 });

    console.log('Capturing Home & Login...');
    await page.goto('http://localhost:8001/', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: `${outDir}/1_Home.png`, fullPage: true });

    await page.goto('http://localhost:8001/login', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: `${outDir}/2_Login.png`, fullPage: true });

    // Admin Login
    console.log('Logging in as Admin...');
    await page.type('#email', 'admin@gmail.com');
    await page.type('#password', 'password123');
    await page.keyboard.press('Enter');
    await page.waitForNavigation({ waitUntil: 'networkidle2' });

    console.log('Capturing Admin pages...');
    await page.screenshot({ path: `${outDir}/3_Admin_Dashboard.png`, fullPage: true });

    const adminRoutes = [
        { name: 'Admin_Client_Index', url: '/admin/client/index' },
        { name: 'Admin_Client_Create', url: '/admin/client/create' },
        { name: 'Admin_Charges_Index', url: '/admin/charges/index' },
        { name: 'Admin_Charges_Create', url: '/admin/charges/create' },
        { name: 'Admin_Dokumen_Index', url: '/admin/dokumen/index' },
        { name: 'Admin_Verifikasi_Index', url: '/admin/verifikasi/index' },
        { name: 'Admin_Laporan_Index', url: '/admin/laporan/index' },
        { name: 'Admin_Riwayat_Index', url: '/admin/riwayat/index' }
    ];

    for (let i = 0; i < adminRoutes.length; i++) {
        await page.goto(`http://localhost:8001${adminRoutes[i].url}`, { waitUntil: 'networkidle2' });
        await page.screenshot({ path: `${outDir}/4_${adminRoutes[i].name}.png`, fullPage: true });
    }

    // Logout
    console.log('Logging out...');
    await page.goto('http://localhost:8001/admin/dashboard', { waitUntil: 'networkidle2' });
    // This is livewire, logout might be a POST request or button click. Let's just clear cookies.
    const client = await page.target().createCDPSession();
    await client.send('Network.clearBrowserCookies');

    // Client Login
    console.log('Logging in as Client...');
    await page.goto('http://localhost:8001/login', { waitUntil: 'networkidle2' });
    await page.type('#email', 'client1@gmail.com');
    await page.type('#password', 'password123');
    await page.keyboard.press('Enter');
    await page.waitForNavigation({ waitUntil: 'networkidle2' });

    console.log('Capturing Client pages...');
    await page.screenshot({ path: `${outDir}/5_Client_Dashboard.png`, fullPage: true });

    const clientRoutes = [
        { name: 'Client_Pengajuan_Index', url: '/client/pengajuan/index' },
        { name: 'Client_Pengajuan_Create', url: '/client/pengajuan/create' }
    ];

    for (let i = 0; i < clientRoutes.length; i++) {
        await page.goto(`http://localhost:8001${clientRoutes[i].url}`, { waitUntil: 'networkidle2' });
        await page.screenshot({ path: `${outDir}/6_${clientRoutes[i].name}.png`, fullPage: true });
    }

    await browser.close();
    console.log('All screenshots captured in ./screenshots folder.');
})();
