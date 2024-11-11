import esbuild from 'esbuild';
import { readdirSync, existsSync, mkdirSync, statSync } from 'fs';
import path from 'path';

// Determine if running in development mode
const isDev = process.argv.includes('--dev');

// Directory paths
const srcDir = path.join(path.resolve(), 'assets/js/src');
const outDir = path.join(path.resolve(), 'assets/js');

// Ensure output directory exists
if (!existsSync(outDir)) {
	mkdirSync(outDir, { recursive: true });
}

// Recursively find all files in the source JS directory
const getAllFiles = (dir) => {
	const files = readdirSync(dir);
	let filelist = [];
	files.forEach(file => {
		const filePath = path.join(dir, file);
		const fileStat = statSync(filePath);
		if (fileStat.isDirectory()) {
			filelist = filelist.concat(getAllFiles(filePath));
		} else if (file.endsWith('.js') || file.endsWith('.ts') || file.endsWith('.tsx')) {
			filelist.push(filePath);
		}
	});
	return filelist;
};

// Get all JavaScript and TypeScript files
const files = getAllFiles(srcDir);

files.forEach(file => {
	const relativePath = path.relative(srcDir, file);
	const outputPath = path.join(outDir, relativePath.replace(/\.(js|ts|tsx)$/, '.min.js'));
	const outputDir = path.dirname(outputPath);

	if (!existsSync(outputDir)) {
		mkdirSync(outputDir, { recursive: true });
	}

	esbuild.build({
		entryPoints: [file],
		outfile: outputPath,
		minify: !isDev,
		sourcemap: isDev,
		bundle: true,
		target: ['es6'], // Adjust based on your target environments
		loader: {
			'.js': 'jsx',
			'.ts': 'ts',
			'.tsx': 'tsx',
		},
	}).catch(() => process.exit(1));
});
