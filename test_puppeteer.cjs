const puppeteer = require('puppeteer');
(async () => {
    try {
        const browser = await puppeteer.launch({headless: 'new'});
        const page = await browser.newPage();
        
        console.log('Navigating to login...');
        await page.goto('http://127.0.0.1:8000/login');
        
        console.log('Logging in...');
        await page.type('input[name=email]', 'made.surya@student.unud.ac.id');
        await page.type('input[name=password]', 'password123');
        await page.click('button[type=submit]');
        await page.waitForNavigation();
        
        console.log('Navigating to proposal list...');
        await page.goto('http://127.0.0.1:8000/mahasiswa/proposal');
        
        const content = await page.content();
        if (content.includes('Testing Judul Porposal 1')) {
            console.log('=== FOUND IN BROWSER ===');
        } else {
            console.log('=== NOT FOUND IN BROWSER ===');
            if (content.includes('Belum Ada Proposal')) {
                console.log('Shows empty state.');
            }
        }
        
        await browser.close();
    } catch (e) {
        console.error(e);
    }
})();
