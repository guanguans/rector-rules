<!--- BEGIN HEADER -->
# Changelog

All notable changes to this project will be documented in this file.
<!--- END HEADER -->

<a name="unreleased"></a>
## [Unreleased]


<a name="1.4.0"></a>
## [1.4.0] - 2026-01-19
### ✨ Features
- **class:** Add UpdatePHPStanMethodNodeParamDocblockFromNodeTypesRector ([4b0c168](https://github.com/guanguans/rector-rules/commit/4b0c168))
- **class:** Add UpdateClassMethodNodeParamDocblockFromNodeTypesRector ([1a40dae](https://github.com/guanguans/rector-rules/commit/1a40dae))

### 📖 Documents
- Update project description for clarity ([e3894b3](https://github.com/guanguans/rector-rules/commit/e3894b3))

### 💅 Code Refactorings
- **class:** Rename UpdateRectorRefactorParamDocblockFromNodeTypesRector ([7d2003e](https://github.com/guanguans/rector-rules/commit/7d2003e))
- **rule:** Rename and restructure ForbiddenSideEffectsRule ([6a20b41](https://github.com/guanguans/rector-rules/commit/6a20b41))


<a name="1.3.0"></a>
## [1.3.0] - 2026-01-14
### ✨ Features
- **array:** Add SortListItemOfSameTypeRector for sorting array items ([202e6e4](https://github.com/guanguans/rector-rules/commit/202e6e4))

### 🐞 Bug Fixes
- **array:** Remove unnecessary Collection dependency and improve key check ([7e70eef](https://github.com/guanguans/rector-rules/commit/7e70eef))

### 📖 Documents
- **README:** Update usage example in README ([0e00f3d](https://github.com/guanguans/rector-rules/commit/0e00f3d))

### 💅 Code Refactorings
- **SetList:** Rename all.php to common.php and add rector.php ([3b9fea3](https://github.com/guanguans/rector-rules/commit/3b9fea3))
- **array:** Improve array item handling in SortListItemOfSameTypeRector ([60baf4a](https://github.com/guanguans/rector-rules/commit/60baf4a))
- **array:** Rename sort_callback to sort_comparator and add sort_direction ([ffb87fb](https://github.com/guanguans/rector-rules/commit/ffb87fb))
- **array:** Replace strcmp with spaceship operator for comparisons ([64095a5](https://github.com/guanguans/rector-rules/commit/64095a5))
- **common:** Remove unused imports and update README example ([e7ee465](https://github.com/guanguans/rector-rules/commit/e7ee465))


<a name="1.2.1"></a>
## [1.2.1] - 2026-01-10
### ✨ Features
- **exception:** Add RectorError class for handling exceptions ([d3db14f](https://github.com/guanguans/rector-rules/commit/d3db14f))

### 💅 Code Refactorings
- **RenameToPsrNameRector:** Simplify renamer logic ([a291079](https://github.com/guanguans/rector-rules/commit/a291079))
- **RenameToPsrNameRector:** Simplify instance checks for UseItem ([b9604e4](https://github.com/guanguans/rector-rules/commit/b9604e4))


<a name="1.2.0"></a>
## [1.2.0] - 2026-01-09
### ✨ Features
- **File:** Add SortFuncDefinitionsRector ([b8c3a1b](https://github.com/guanguans/rector-rules/commit/b8c3a1b))
- **config:** Add configuration files for Rector rules ([2a4c3bb](https://github.com/guanguans/rector-rules/commit/2a4c3bb))

### 💅 Code Refactorings
- **File:** Improve sorting logic in SortFuncDefinitionsRector ([83e9f8b](https://github.com/guanguans/rector-rules/commit/83e9f8b))
- **RenameToPsrNameRector:** Improve return logic for node processing ([d199d62](https://github.com/guanguans/rector-rules/commit/d199d62))


<a name="1.1.0"></a>
## [1.1.0] - 2026-01-07
### ✨ Features
- **File:** Add SortFileNoinspectionDocblockRector ([a52c37c](https://github.com/guanguans/rector-rules/commit/a52c37c))

### 💅 Code Refactorings
- **Declare:** Rename AddNoinspectionDocblockToDeclareRector ([6c95725](https://github.com/guanguans/rector-rules/commit/6c95725))
- **UpdateRectorCodeSamplesFromFixturesRector:** Improve scope handling ([286baf0](https://github.com/guanguans/rector-rules/commit/286baf0))


<a name="1.0.1"></a>
## [1.0.1] - 2026-01-06
### ✨ Features
- **Class:** Add UpdateParameterTypeOfRectorRefactorMethodRector ([e687de1](https://github.com/guanguans/rector-rules/commit/e687de1))
- **Rector:** Add UpdateCodeSamplesRector for code sample updates ([d97c3dc](https://github.com/guanguans/rector-rules/commit/d97c3dc))

### 💅 Code Refactorings
- apply phpstan ([1ba240e](https://github.com/guanguans/rector-rules/commit/1ba240e))
- **tests:** Remove custom bootstrap and update test configuration ([af219bf](https://github.com/guanguans/rector-rules/commit/af219bf))
- **tests:** Remove unused tests and simplify logic ([b21d32f](https://github.com/guanguans/rector-rules/commit/b21d32f))

### ✅ Tests
- **rector:** Add testing for rector ([6d220c4](https://github.com/guanguans/rector-rules/commit/6d220c4))


<a name="1.0.0"></a>
## 1.0.0 - 2026-01-03
### 🎨 Styles
- **copyright:** Update copyright years to 2025-2026 in headers and LICENSE ([4e2c54f](https://github.com/guanguans/rector-rules/commit/4e2c54f))

### 💅 Code Refactorings
- apply inspection ([7cc9d1f](https://github.com/guanguans/rector-rules/commit/7cc9d1f))
- **AbstractRector:** Remove unused classes method and simplify logic ([6dcf116](https://github.com/guanguans/rector-rules/commit/6dcf116))
- **Contract:** Remove ThrowableContract and RuntimeException ([5e3dab9](https://github.com/guanguans/rector-rules/commit/5e3dab9))
- **Rector:** Improve node collection and remove unused code ([a3c069a](https://github.com/guanguans/rector-rules/commit/a3c069a))
- **RemoveNamespaceRector:** Simplify node collection logic ([f032aa8](https://github.com/guanguans/rector-rules/commit/f032aa8))
- **RenameToPsrNameRector:** Improve error handling and dependency injection ([0286737](https://github.com/guanguans/rector-rules/commit/0286737))

### ✅ Tests
- **rules:** Add RenameToPsrNameRector.php and associated tests ([3514b1d](https://github.com/guanguans/rector-rules/commit/3514b1d))
- **rules:** Add SimplifyListIndexRectorTest ([0d42551](https://github.com/guanguans/rector-rules/commit/0d42551))

### 📦 Builds
- **deps-dev:** Update rector/jack requirement || ^0.5 ([90cfd71](https://github.com/guanguans/rector-rules/commit/90cfd71))

### Pull Requests
- Merge pull request [#1](https://github.com/guanguans/rector-rules/issues/1) from guanguans/dependabot/composer/rector/jack-tw-0.4or-tw-0.5


[Unreleased]: https://github.com/guanguans/rector-rules/compare/1.4.0...HEAD
[1.4.0]: https://github.com/guanguans/rector-rules/compare/1.3.0...1.4.0
[1.3.0]: https://github.com/guanguans/rector-rules/compare/1.2.1...1.3.0
[1.2.1]: https://github.com/guanguans/rector-rules/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/guanguans/rector-rules/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/guanguans/rector-rules/compare/1.0.1...1.1.0
[1.0.1]: https://github.com/guanguans/rector-rules/compare/1.0.0...1.0.1
