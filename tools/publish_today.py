# Harbour One Group: publish today's scheduled social post.
# Runs inside the Composio remote workbench (helpers run_composio_tool / upload_local_file are preloaded).
import requests, json, os, datetime
from zoneinfo import ZoneInfo

BASE = "https://guide.harbour-one-group.co.uk/social/"
LINKEDIN_AUTHOR = "urn:li:person:-TkT13IHo9"
LOG = "/mnt/files/hog_social_log.json"

def _data(res):
    d = res
    for k in ("data", "response", "data"):
        if isinstance(d, dict) and k in d: d = d[k]
    return d

def publish_today(force_date=None, dry_run=False):
    today = force_date or datetime.datetime.now(ZoneInfo("Europe/London")).date().isoformat()
    sched = requests.get(BASE + "schedule.json", timeout=60).json()
    post = next((p for p in sched if p["date"] == today), None)
    report = {"date": today, "post": None, "linkedin": None, "facebook": None, "instagram": None}
    if not post:
        report["post"] = "nothing scheduled today"; return report
    report["post"] = f'{post["n"]} {post["pillar"]}: {post["headline"]}'
    log = json.load(open(LOG)) if os.path.exists(LOG) else {}
    done = log.get(today, {})

    img = f"/home/user/hog_{post['n']}.jpg"
    r = requests.get(post["image"], timeout=60)
    if r.status_code != 200 or not r.headers.get("content-type", "").startswith("image/"):
        report["error"] = f"image not reachable ({r.status_code})"; return report
    open(img, "wb").write(r.content)
    if dry_run:
        report["dry_run"] = True; return report

    # LinkedIn
    if done.get("linkedin"):
        report["linkedin"] = "already posted " + done["linkedin"]
    else:
        up, err = upload_local_file(img)
        if err: report["linkedin"] = "upload failed: " + str(err)
        else:
            res, err = run_composio_tool("LINKEDIN_CREATE_LINKED_IN_POST", {
                "author": LINKEDIN_AUTHOR, "commentary": post["caption"][:2990], "visibility": "PUBLIC",
                "lifecycleState": "PUBLISHED",
                "images": [{"name": post["n"] + ".jpg", "mimetype": "image/jpeg", "s3key": up["s3key"]}]})
            if err: report["linkedin"] = "failed: " + str(err)[:300]
            else:
                done["linkedin"] = str(_data(res)); report["linkedin"] = "posted " + done["linkedin"]

    # Facebook Page (only once the Page exists and is connected)
    if done.get("facebook"):
        report["facebook"] = "already posted"
    else:
        res, err = run_composio_tool("FACEBOOK_LIST_MANAGED_PAGES", {"fields": "id,name"})
        pages = _data(res) if not err else []
        if isinstance(pages, dict): pages = pages.get("data", [])
        page = next((p for p in (pages or []) if "harbour" in p.get("name", "").lower()), None)
        if not page:
            report["facebook"] = "skipped: no Harbour One Group Page connected yet"
        else:
            res, err = run_composio_tool("FACEBOOK_CREATE_PHOTO_POST", {"page_id": page["id"], "url": post["image"], "message": post["caption"], "published": True})
            if err: report["facebook"] = "failed: " + str(err)[:300]
            else:
                done["facebook"] = str(_data(res))[:200]; report["facebook"] = "posted"

    # Instagram (only once a Business/Creator account is connected)
    if done.get("instagram"):
        report["instagram"] = "already posted"
    else:
        res, err = run_composio_tool("INSTAGRAM_GET_USER_INFO", {"ig_user_id": "me"})
        if err:
            report["instagram"] = "skipped: Instagram not connected yet"
        else:
            info = _data(res); igid = (info or {}).get("id") if isinstance(info, dict) else None
            if not igid:
                report["instagram"] = "skipped: could not read Instagram account id"
            else:
                c, err = run_composio_tool("INSTAGRAM_POST_IG_USER_MEDIA", {"ig_user_id": igid, "image_url": post["image"], "caption": post["caption_instagram"]})
                cid = (_data(c) or {}).get("id") if not err and isinstance(_data(c), dict) else None
                if not cid: report["instagram"] = "failed creating media: " + str(err or c)[:300]
                else:
                    p, err = run_composio_tool("INSTAGRAM_POST_IG_USER_MEDIA_PUBLISH", {"ig_user_id": igid, "creation_id": cid, "max_wait_seconds": 90})
                    if err: report["instagram"] = "failed publishing: " + str(err)[:300]
                    else:
                        done["instagram"] = str(_data(p))[:200]; report["instagram"] = "posted"

    log[today] = done
    json.dump(log, open(LOG, "w"))
    return report
