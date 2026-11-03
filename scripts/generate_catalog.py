#!/usr/bin/env python3
"""
Generates discovery indexes in docs/ from metadata/ files:
1. docs/DISCOVERY_BY_DSA.md: Discover by theoretical computer science category
2. docs/DISCOVERY_BY_USE_CASE.md: Discover by real-world engineering challenge
"""
import json
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent
METADATA_DIR = ROOT_DIR / "metadata"
DOCS_DIR = ROOT_DIR / "docs"

def load_json(filepath):
    with open(filepath, "r", encoding="utf-8") as f:
        return json.load(f)

def generate():
    DOCS_DIR.mkdir(exist_ok=True)
    categories = load_json(METADATA_DIR / "categories.json")
    use_cases = load_json(METADATA_DIR / "use-cases.json")
    components = load_json(METADATA_DIR / "components.json")

    cat_map = {c["id"]: c for c in categories}
    uc_map = {u["id"]: u for u in use_cases}

    # Group components by DSA Category
    comp_by_cat = {c["id"]: [] for c in categories}
    for comp in components:
        comp_by_cat[comp["dsa_category"]].append(comp)
        sec = comp.get("secondary_dsa_category")
        if sec and sec in comp_by_cat:
            comp_by_cat[sec].append(comp)

    # Group components by Use Case
    comp_by_uc = {u["id"]: [] for u in use_cases}
    for comp in components:
        for u in comp.get("use_cases", []):
            if u in comp_by_uc:
                comp_by_uc[u].append(comp)

    # 1. DISCOVERY_BY_DSA.md
    dsa_lines = [
        "# Component Discovery by Traditional DSA Category",
        "",
        "> Explore reusable, dependency-free DSA components categorized by theoretical Computer Science classification.",
        "",
        "## Quick Navigation",
        ""
    ]
    for cat in categories:
        count = len(comp_by_cat[cat["id"]])
        dsa_lines.append(f"- [{cat['name']}](#{cat['id']}) ({count} component{'s' if count != 1 else ''})")
    dsa_lines.append("")

    for cat in categories:
        cat_comps = comp_by_cat[cat["id"]]
        dsa_lines.append(f"## {cat['name']} <a id='{cat['id']}'></a>")
        dsa_lines.append(f"{cat['description']}\n")
        dsa_lines.append("**Key Characteristics:** " + ", ".join(cat.get("characteristics", [])) + "\n")
        if not cat_comps:
            dsa_lines.append("*No components currently registered in this category.*\n")
            continue

        dsa_lines.append("| Component | Difficulty | Time Complexity | Space Complexity | Real-World Use Cases | Implementations |")
        dsa_lines.append("|---|---|---|---|---|---|")
        for c in cat_comps:
            time_summary = ", ".join(f"{k}: `{v}`" for k, v in list(c["time_complexity"].items())[:2])
            use_case_names = ", ".join(uc_map[u]["name"] for u in c["use_cases"][:2])
            impls = []
            if "php" in c["implementations"]:
                impls.append(f"[PHP](../{c['implementations']['php']})")
            if "javascript" in c["implementations"]:
                impls.append(f"[JS](../{c['implementations']['javascript']})")
            if "python" in c["implementations"]:
                impls.append(f"[Python](../{c['implementations']['python']})")
            if "java" in c["implementations"]:
                impls.append(f"[Java](../{c['implementations']['java']})")
            doc_link = f"[{c['name']}](components/{c['id']}.md)"
            dsa_lines.append(f"| {doc_link} | `{c['difficulty']}` | {time_summary} | `{c['space_complexity']}` | {use_case_names} | {' · '.join(impls)} |")
        dsa_lines.append("")

    with open(DOCS_DIR / "DISCOVERY_BY_DSA.md", "w", encoding="utf-8") as f:
        f.write("\n".join(dsa_lines) + "\n")

    # 2. DISCOVERY_BY_USE_CASE.md
    uc_lines = [
        "# Component Discovery by Real-World Engineering Use Case",
        "",
        "> Select the engineering problem or system requirement you need to solve to find production-tested, dependency-free DSA components.",
        "",
        "## Quick Navigation",
        ""
    ]
    for uc in use_cases:
        count = len(comp_by_uc[uc["id"]])
        uc_lines.append(f"- [{uc['name']}](#{uc['id']}) ({count} component{'s' if count != 1 else ''})")
    uc_lines.append("")

    for uc in use_cases:
        uc_comps = comp_by_uc[uc["id"]]
        uc_lines.append(f"## {uc['name']} <a id='{uc['id']}'></a>")
        uc_lines.append(f"{uc['description']}\n")
        if "challenges_solved" in uc:
            uc_lines.append("**Challenges Addressed:**")
            for ch in uc["challenges_solved"]:
                uc_lines.append(f"- {ch}")
            uc_lines.append("")

        if not uc_comps:
            uc_lines.append("*No components currently registered for this use case.*\n")
            continue

        uc_lines.append("| Component | DSA Classification | Key Advantage | Implementations | Guide |")
        uc_lines.append("|---|---|---|---|---|")
        for c in uc_comps:
            dsa_name = cat_map[c["dsa_category"]]["name"]
            impls = []
            if "php" in c["implementations"]:
                impls.append(f"[PHP](../{c['implementations']['php']})")
            if "javascript" in c["implementations"]:
                impls.append(f"[JS](../{c['implementations']['javascript']})")
            if "python" in c["implementations"]:
                impls.append(f"[Python](../{c['implementations']['python']})")
            if "java" in c["implementations"]:
                impls.append(f"[Java](../{c['implementations']['java']})")
            adv = c["when_to_use"][0] if c.get("when_to_use") else c["description"]
            guide_link = f"[Read Spec & Usage](components/{c['id']}.md)"
            uc_lines.append(f"| **{c['name']}** | {dsa_name} | {adv} | {' · '.join(impls)} | {guide_link} |")
        uc_lines.append("")

    with open(DOCS_DIR / "DISCOVERY_BY_USE_CASE.md", "w", encoding="utf-8") as f:
        f.write("\n".join(uc_lines) + "\n")

    print("Successfully generated docs/DISCOVERY_BY_DSA.md and docs/DISCOVERY_BY_USE_CASE.md")

if __name__ == "__main__":
    generate()

