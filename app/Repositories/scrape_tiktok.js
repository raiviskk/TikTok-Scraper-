const {chromium} = require('playwright');
const fs = require('fs/promises');
const {join} = require("path");


const userAgent =
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36';

async function scrapeTikTokContent(searches) {
    const browser = await chromium.launch({headless: false});
    const context = await browser.newContext({
        userAgent,
    });
    const page = await context.newPage();


    await page.context().clearCookies();


    await page.goto('https://www.tiktok.com', {waitUntil: 'load'});


    await page.waitForTimeout(5 * 1000);


    let allProfileData = [];

    for (const search of searches) {

        await page.waitForTimeout(5 * 1000);


        await page.click('input[name="q"]');
        await page.waitForTimeout(2000);

        // Simulate pressing the Control on Windows or Command (Meta) key on Mac
        // Note: You may need to adjust the key based on your specific requirements
        await page.keyboard.down('Meta');
        await page.keyboard.press('A');
        await page.keyboard.up('Meta');

        await page.waitForTimeout(2000);


        await page.keyboard.press('Backspace');
        await page.waitForTimeout(2000);


        await page.type('input[name="q"]', search);
        await page.press('input[name="q"]', 'Enter');


        await page.waitForTimeout(7 * 1000);


        const profileUrls = await page.$$eval('a', (links) => {
            const uniqueUrls = new Set();
            return links
                .map((link) => `https://www.tiktok.com${link.pathname}`)
                .filter((url) => url.startsWith('https://www.tiktok.com/@') && !url.includes('/video/') && url !== 'https://www.tiktok.com/@')
                .filter((url) => {
                    if (!uniqueUrls.has(url)) {
                        uniqueUrls.add(url);
                        return true;
                    }
                    return false;
                });
        });


        for (const profileUrl of profileUrls) {
            // Open the profile page
            await page.goto(profileUrl, {});


            await page.waitForTimeout(5000);


            const profileData = await page.evaluate(() => {
                const getNumericValue = (str) => {
                    const multiplier = {k: 1000, m: 1000000};
                    const match = str.match(/([\d.]+)\s*([kKmM])?/);
                    if (match) {
                        const numericValue = parseFloat(match[1]);
                        const suffix = match[2]?.toLowerCase();
                        return suffix ? numericValue * multiplier[suffix] : numericValue;
                    }
                    return null;
                };

                const getLastFiveVideosLikes = () => {
                    const likeElements = document.querySelectorAll('.tiktok-dirst9-StrongVideoCount');
                    const likes = Array.from(likeElements)
                        .slice(0, 5)
                        .map((element) => getNumericValue(element.textContent))
                        .filter((value) => !isNaN(value));
                    return likes;
                };

                const getSumOfLastFiveVideoViews = () => {
                    const viewElements = document.querySelectorAll('.tiktok-dirst9-StrongVideoCount');
                    const views = Array.from(viewElements)
                        .slice(0, 5)
                        .map((element) => getNumericValue(element.textContent))
                        .filter((value) => !isNaN(value));
                    return views.reduce((sum, value) => sum + value, 0);
                };

                return {
                    profileUrl: window.location.href,
                    followers: getNumericValue(document.querySelector('[data-e2e="followers-count"]').textContent),
                    likes: getNumericValue(document.querySelector('[data-e2e="likes-count"]').textContent),
                    lastFiveVideosLikes: getLastFiveVideosLikes(),
                    sumOfLastFiveVideoViews: getSumOfLastFiveVideoViews(),
                };
            });


            allProfileData.push(profileData);
        }
    }


    const filePath = join(__dirname, 'tiktok_profiles.json');
    await fs.writeFile(filePath, JSON.stringify(allProfileData), 'utf-8');


    await browser.close();
}

const hashtags = process.argv.slice(2);

scrapeTikTokContent(hashtags)
    .then(() => console.log('Scraping complete'))
    .catch((error) => console.error(error));
