import js from '@eslint/js'
import globals from 'globals'
import reactHooks from 'eslint-plugin-react-hooks'
import reactRefresh from 'eslint-plugin-react-refresh'
import { defineConfig, globalIgnores } from 'eslint/config'

export default defineConfig([
  globalIgnores(['dist', '../plugins/pi-dashboard/assets/app/**', 'node_modules', 'e2e']),

  // App source: browser globals, React/Hooks rules.
  {
    files: ['src/**/*.{js,jsx}'],
    extends: [
      js.configs.recommended,
      reactHooks.configs.flat.recommended,
      reactRefresh.configs.vite,
    ],
    languageOptions: {
      ecmaVersion: 'latest',
      globals: globals.browser,
      parserOptions: {
        ecmaFeatures: { jsx: true },
        sourceType: 'module',
      },
    },
    rules: {
      // Allow underscore-prefixed args/vars to indicate intentional skip.
      'no-unused-vars': ['error', {
        argsIgnorePattern: '^_',
        varsIgnorePattern: '^[_A-Z]',
        caughtErrorsIgnorePattern: '^_',
        destructuredArrayIgnorePattern: '^_',
      }],
      // HMR correctness only; not a bug — demote to warn so build/CI not blocked.
      // allowConstantExport lets files mix a component default-export with
      // co-located `export const FOO = ...` constants (HMR can still reload).
      'react-refresh/only-export-components': ['warn', { allowConstantExport: true }],
      // Useful but very noisy on intentional 'on mount only' effects.
      'react-hooks/exhaustive-deps': 'warn',
      // Common for "load data on mount" patterns; legit cascade risk but
      // not a hard bug. Keep as warn so we can audit without blocking CI.
      'react-hooks/set-state-in-effect': 'warn',
      // React Compiler strict rules — useful signal but legacy code has
      // many real cases. Demote to warn for incremental cleanup.
      'react-hooks/static-components': 'warn',  // inline component in render
      'react-hooks/purity': 'warn',              // Math.random / Date.now in render
      'react-hooks/immutability': 'warn',        // var-before-declare
      'react-hooks/refs': 'warn',
    },
  },

  // Test files: add Node + Vitest globals (vi, expect, test, describe...).
  {
    files: ['src/**/*.test.{js,jsx}', 'src/test/**/*.{js,jsx}'],
    languageOptions: {
      globals: { ...globals.browser, ...globals.node, ...globals.vitest },
    },
  },

  // Config files (vite, vitest): Node globals.
  {
    files: ['*.config.{js,ts,mjs}'],
    languageOptions: {
      globals: { ...globals.node },
    },
  },
])
