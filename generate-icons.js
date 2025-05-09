const sharp = require('sharp');
const { readFileSync } = require('fs');
const path = require('path');

const inputSvg = readFileSync(path.join(__dirname, 'public', 'images', 'logo.svg'));
const sizes = [192, 512];

async function generateIcons() {
    for (const size of sizes) {
        await sharp(inputSvg)
            .resize(size, size)
            .png()
            .toFile(path.join(__dirname, 'public', 'images', `icon-${size}x${size}.png`));
        console.log(`Generated ${size}x${size} icon`);
    }
}

generateIcons().catch(console.error);
