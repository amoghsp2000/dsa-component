# Contributing to DSA Toolbox

Thank you for your interest in contributing to **DSA Toolbox**! 

DSA Toolbox is an open-source, multi-language collection of **dependency-free, standalone Data Structures and Algorithms** categorized by both theoretical computer science classifications and **real-world engineering use cases**.

---

## 1. Core Principles

1. **Standalone & Zero-Dependency**: Every component must be self-contained in a single file or a minimal cohesion unit. A developer should be able to copy the file directly into their codebase without installing Composer, npm, pip, or Maven packages.
2. **Dual Classification**: Every component must be categorized under:
   - A **Traditional DSA Category** (e.g., *Hash Tables*, *Stacks and Queues*, *Trees and Graphs*)
   - One or more **Real-World Engineering Use Cases** (e.g., *Caching and Memory Management*, *Rate Limiting and Throttling*, *Financial and Payment Systems*)
3. **Multi-Language Equivalence**: Implementations must be provided across all supported languages (PHP, JavaScript, Python, Java) with consistent method semantics, while respecting idiomatic conventions.
4. **Production Quality**: Clear docblocks, explicit time/space complexity annotations, edge-case coverage, input validation, and unit tests.
5. **Practical Integration Focus**: Every component must demonstrate realistic usage (e.g., payment deduplication, recharge scheduling, API caching) rather than just trivial synthetic examples.

---

## 2. Directory Structure

```text
dsa-toolbox/
├── metadata/                  # Central metadata catalog
│   ├── categories.json        # Traditional DSA categories
│   ├── use-cases.json         # Real-world engineering use cases
│   └── components.json        # Master component registry
├── docs/                      # Dual-discovery guides and component docs
│   ├── DISCOVERY_BY_DSA.md
│   ├── DISCOVERY_BY_USE_CASE.md
│   └── components/*.md
├── php/                       # Standalone PHP 8.x implementations & examples
│   ├── src/*.php
│   └── examples/*.php
├── javascript/                # Modern ES6+ / Node.js implementations & examples
│   ├── src/*.js
│   └── examples/*.js
├── python/                    # Python 3.9+ implementations (PEP 8 + type hints)
│   ├── src/*.py
│   └── examples/*.py
├── java/                      # Clean Java 11+ implementations
│   ├── src/main/java/com/dsatoolbox/*.java
│   └── examples/*.java
├── tests/                     # Unit test suites across all 4 languages
│   ├── php/
│   ├── javascript/
│   ├── python/
│   └── java/
└── scripts/                   # Automation scripts for metadata validation & catalog sync
```

---

## 3. Language Standards & Conventions

| Language | Standard | Conventions |
|---|---|---|
| **PHP** | PHP 8.0+ | PSR-12, strict types (`declare(strict_types=1);`), type hints, standalone class inside namespace `DsaToolbox\` or directly importable. |
| **JavaScript** | ES2020+ | CommonJS & ESM compatible exports, JSDoc annotations, clean class/prototype structures, zero external packages. |
| **Python** | Python 3.9+ | PEP 8, full `typing` hints, dataclasses/classes with descriptive docstrings, zero non-stdlib dependencies. |
| **Java** | Java 11+ | Clean OOP, generic classes, descriptive Javadoc, standalone in `com.dsatoolbox` package. |

---

## 4. Component Contribution Checklist

When introducing a new component or algorithm:

- [ ] **Implementation**: Implement the component in PHP, JavaScript, Python, and Java.
- [ ] **Comments & Complexity**: Document worst/average/best time complexity and space complexity.
- [ ] **Input Validation & Edge Cases**: Explicitly handle empty inputs, out-of-bounds, duplicates, nulls, and overflow conditions.
- [ ] **Unit Tests**: Add tests covering positive paths, negative paths, boundary conditions, and stress cases.
- [ ] **Metadata Entry**: Register the component in `metadata/components.json` with its DSA category, use cases, complexity, and file paths.
- [ ] **Documentation**: Create `docs/components/<component-slug>.md` highlighting:
  - Theoretical overview
  - Real-world engineering use case
  - When to use / When NOT to use
  - Copy-paste ready snippets in all 4 languages
  - Practical enterprise example
- [ ] **Catalog Re-generation**: Run `python scripts/validate_metadata.py` and `python scripts/generate_catalog.py` to keep documentation indexes synchronized.

---

## 5. Development Workflow

1. Fork the repository and create your feature branch:
   ```bash
   git checkout -b feature/component-name
   ```
2. Implement your component and tests.
3. Validate metadata and run tests:
   ```bash
   python scripts/validate_metadata.py
   python scripts/run_python_tests.py
   ```
4. Commit with descriptive semantic messages (`feat: add Consistent Hashing in 4 languages`).
5. Open a Pull Request detailing the engineering problem solved.

