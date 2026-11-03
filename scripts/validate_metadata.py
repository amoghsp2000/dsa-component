#!/usr/bin/env python3
"""
Validates metadata JSON schemas in the metadata/ directory.
Ensures every component is linked to valid DSA categories and Use Cases,
and checks for required fields.
"""
import json
import sys
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent
METADATA_DIR = ROOT_DIR / "metadata"

def load_json(filepath):
    if not filepath.exists():
        print(f"Error: {filepath} does not exist.")
        sys.exit(1)
    with open(filepath, "r", encoding="utf-8") as f:
        return json.load(f)

def validate():
    categories = load_json(METADATA_DIR / "categories.json")
    use_cases = load_json(METADATA_DIR / "use-cases.json")
    components = load_json(METADATA_DIR / "components.json")

    cat_ids = {c["id"] for c in categories}
    uc_ids = {u["id"] for u in use_cases}

    print(f"Loaded {len(categories)} categories, {len(use_cases)} use cases, {len(components)} components.")

    required_fields = [
        "id", "name", "description", "dsa_category", "use_cases",
        "difficulty", "time_complexity", "space_complexity",
        "when_to_use", "when_not_to_use", "edge_cases",
        "implementations", "tests", "documentation"
    ]

    errors = []
    component_ids = set()

    for comp in components:
        cid = comp.get("id")
        if not cid:
            errors.append("Component missing 'id'")
            continue
        if cid in component_ids:
            errors.append(f"Duplicate component id '{cid}'")
        component_ids.add(cid)

        for field in required_fields:
            if field not in comp:
                errors.append(f"Component '{cid}' is missing required field '{field}'")

        dsa_cat = comp.get("dsa_category")
        if dsa_cat not in cat_ids:
            errors.append(f"Component '{cid}' has unknown dsa_category '{dsa_cat}'")

        sec_cat = comp.get("secondary_dsa_category")
        if sec_cat and sec_cat not in cat_ids:
            errors.append(f"Component '{cid}' has unknown secondary_dsa_category '{sec_cat}'")

        for uc in comp.get("use_cases", []):
            if uc not in uc_ids:
                errors.append(f"Component '{cid}' references unknown use case '{uc}'")

    if errors:
        print("Metadata validation FAILED with errors:")
        for err in errors:
            print(f" - {err}")
        sys.exit(1)
    else:
        print("Metadata validation PASSED! All categories, use-cases, and components are consistent.")

if __name__ == "__main__":
    validate()

