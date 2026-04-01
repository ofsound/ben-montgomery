import { readFileSync, writeFileSync } from 'node:fs';
import path from 'node:path';

const blueprintBuilderTarget = path.resolve(
  'node_modules/@wordpress/env/lib/runtime/playground/blueprint-builder.js'
);
const playgroundRuntimeTarget = path.resolve(
  'node_modules/@wordpress/env/lib/runtime/playground/index.js'
);

function patchBlueprintBuilder() {
  const original = readFileSync(blueprintBuilderTarget, 'utf8');

  if (
    original.includes(
      'wp-env Playground config constants patched for SQLite compatibility.'
    )
  ) {
    return;
  }

  const pattern =
    /[ \t]*\/\/ Configure wp-config constants[\s\S]*?\n[ \t]*\/\/ Handle multisite/;

  if (!pattern.test(original)) {
    throw new Error(
      `Could not find Playground config constant block in ${blueprintBuilderTarget}`
    );
  }

  const replacement = [
    '\t// wp-env Playground config constants patched for SQLite compatibility.',
    '\t// The generated defineWpConfigConsts step causes SQLITE_MAIN_FILE fatals',
    '\t// in the current wp-env + Playground combination, so omit it here.',
    '',
    '\t// Handle multisite',
  ].join('\n');

  writeFileSync(
    blueprintBuilderTarget,
    original.replace(pattern, replacement),
    'utf8'
  );
}

function patchPlaygroundRuntime() {
  const original = readFileSync(playgroundRuntimeTarget, 'utf8');

  let patched = original;

  const originalCheckServerBlock = [
    '\t_checkServer( port ) {',
    '\t\treturn new Promise( ( resolve, reject ) => {',
    '\t\t\tconst req = http.get( `http://localhost:${ port }`, ( res ) => {',
    '\t\t\t\tif ( res.statusCode >= 200 && res.statusCode < 400 ) {',
    '\t\t\t\t\tresolve();',
    '\t\t\t\t} else {',
    '\t\t\t\t\treject( new Error( `Status: ${ res.statusCode }` ) );',
    '\t\t\t\t}',
    '\t\t\t} );',
    "\t\t\treq.on( 'error', reject );",
    '\t\t\treq.setTimeout( 1000, () => {',
    '\t\t\t\treq.destroy();',
    "\t\t\t\treject( new Error( 'Timeout' ) );",
    '\t\t\t} );',
    '\t\t} );',
    '\t}',
  ].join('\n');

  if (
    !patched.includes(
      'wp-env Playground status probe patched for localhost/127.0.0.1 compatibility.'
    )
  ) {
    if (!patched.includes(originalCheckServerBlock)) {
      throw new Error(
        `Could not find Playground _checkServer block in ${playgroundRuntimeTarget}`
      );
    }

    const checkServerReplacement = [
      '\t_checkServer( port ) {',
      '\t\tconst hosts = [',
      "\t\t\t'localhost',",
      "\t\t\t'127.0.0.1',",
      "\t\t\t'[::1]',",
      '\t\t];',
      '',
      '\t\t// wp-env Playground status probe patched for localhost/127.0.0.1 compatibility.',
      '\t\tconst attempt = ( host ) =>',
      '\t\t\tnew Promise( ( resolve, reject ) => {',
      '\t\t\t\tconst req = http.get( `http://${ host }:${ port }`, ( res ) => {',
      '\t\t\t\t\tif ( res.statusCode >= 200 && res.statusCode < 400 ) {',
      '\t\t\t\t\t\tresolve();',
      '\t\t\t\t\t} else {',
      '\t\t\t\t\t\treject( new Error( `Status: ${ res.statusCode }` ) );',
      '\t\t\t\t\t}',
      '\t\t\t\t} );',
      "\t\t\t\treq.on( 'error', reject );",
      '\t\t\t\treq.setTimeout( 1000, () => {',
      '\t\t\t\t\treq.destroy();',
      "\t\t\t\t\treject( new Error( 'Timeout' ) );",
      '\t\t\t\t} );',
      '\t\t\t} );',
      '',
      '\t\treturn hosts.reduce(',
      '\t\t\t( chain, host ) => chain.catch( () => attempt( host ) ),',
      "\t\t\tPromise.reject( new Error( 'Initial probe' ) )",
      '\t\t);',
      '\t}',
    ].join('\n');

    patched = patched.replace(
      originalCheckServerBlock,
      checkServerReplacement
    );
  }

  const originalStatusBlock = [
    '\t\t// Check if server is running.',
    '\t\tlet isRunning = false;',
    '',
    '\t\ttry {',
    "\t\t\tconst pidContent = await fs.readFile( pidFile, 'utf8' );",
    '\t\t\tconst pid = parseInt( pidContent.trim(), 10 );',
    '',
    '\t\t\t// Check if process is still alive.',
    '\t\t\tprocess.kill( pid, 0 );',
    '',
    '\t\t\t// Check if server is responding.',
    '\t\t\tawait this._checkServer( port );',
    '\t\t\tisRunning = true;',
    '\t\t} catch {',
    '\t\t\t// Process not running or server not responding.',
    '\t\t}',
  ].join('\n');

  if (
    !patched.includes('wp-env Playground running-state detection patched for detached server processes.')
  ) {
    if (!patched.includes(originalStatusBlock)) {
      throw new Error(
        `Could not find Playground getStatus block in ${playgroundRuntimeTarget}`
      );
    }

    const statusReplacement = [
      '\t\t// Check if server is running.',
      '\t\tlet isRunning = false;',
      '',
      '\t\ttry {',
      "\t\t\tconst pidContent = await fs.readFile( pidFile, 'utf8' );",
      '\t\t\tconst pid = parseInt( pidContent.trim(), 10 );',
      '',
      '\t\t\t// Check if process is still alive.',
      '\t\t\tprocess.kill( pid, 0 );',
      '\t\t\tisRunning = true;',
      '',
      '\t\t\t// wp-env Playground running-state detection patched for detached server processes.',
      '\t\t\t// Keep the HTTP probe as a best-effort signal, but do not mark the',
      '\t\t\t// environment stopped when the detached server PID is alive and the',
      '\t\t\t// browser can still load the site.',
      '\t\t\ttry {',
      '\t\t\t\tawait this._checkServer( port );',
      '\t\t\t} catch {}',
      '\t\t} catch {',
      '\t\t\t// Process not running.',
      '\t\t}',
    ].join('\n');

    patched = patched.replace(originalStatusBlock, statusReplacement);
  }

  if (patched !== original) {
    writeFileSync(playgroundRuntimeTarget, patched, 'utf8');
  }
}

patchBlueprintBuilder();
patchPlaygroundRuntime();
