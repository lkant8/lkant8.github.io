import shutil
import os

def on_post_build(config):
    """Copy root-level files verbatim after MkDocs build."""
    files_to_copy = ["app-ads.txt"]
    docs_dir = config["docs_dir"]
    site_dir = config["site_dir"]

    for filename in files_to_copy:
        src = os.path.join(docs_dir, filename)
        dst = os.path.join(site_dir, filename)
        if os.path.exists(src):
            shutil.copy2(src, dst)
            print(f"[hook] Copied '{filename}' → site root")
        else:
            print(f"[hook] WARNING: '{filename}' not found in docs/")