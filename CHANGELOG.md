<!--- BEGIN HEADER -->
# Changelog

All notable changes to this project will be documented in this file.
<!--- END HEADER -->

<a name="unreleased"></a>
## [Unreleased]


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


[Unreleased]: https://github.com/guanguans/rector-rules/compare/1.2.0...HEAD
[1.2.0]: https://github.com/guanguans/rector-rules/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/guanguans/rector-rules/compare/1.0.1...1.1.0
[1.0.1]: https://github.com/guanguans/rector-rules/compare/1.0.0...1.0.1
