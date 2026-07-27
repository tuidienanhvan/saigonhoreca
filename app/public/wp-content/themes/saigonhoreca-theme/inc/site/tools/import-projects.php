<?php
/**
 * Admin tool: Import CPT `project` (30 dự án) + chuẩn hoá trang Về/Liên hệ về
 * template theme (tắt Elementor). Dùng khi go-live: theme đã có data dự án sẵn,
 * không cần upload file WXR lẻ.
 *
 * USAGE (admin, hoặc &token=<SGH_REIMPORT_TOKEN>):
 *   /?sgh_import_projects=1            → DRY: đếm dự án sẽ tạo
 *   /?sgh_import_projects=1&run=1      → tạo dự án còn thiếu + fix 2 trang
 *
 * An toàn: bỏ qua dự án đã tồn tại (chạy lại không trùng). Trang Về/Liên hệ chỉ
 * đổi _wp_page_template (giữ nguyên nội dung Elementor cũ, chỉ ngừng render).
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    if (!isset($_GET['sgh_import_projects'])) return;

    $token_ok = isset($_GET['token']) && defined('SGH_REIMPORT_TOKEN')
        && hash_equals(SGH_REIMPORT_TOKEN, (string) $_GET['token']);
    if (!$token_ok && !current_user_can('manage_options')) {
        wp_die('Admin only.', '', ['response' => 403]);
    }

    @set_time_limit(0);
    header('Content-Type: text/plain; charset=UTF-8');
    while (ob_get_level()) ob_end_flush();

    $is_run = !empty($_GET['run']);
    $projects = json_decode(base64_decode('W3siZGF0ZSI6ICIyMDIzLTA1LTA4IDEwOjMwOjE0IiwgInNsdWciOiAicm9rYS1mZWxsYS10aW5oLWhvYS1hbS10aHVjLW5oYXQtYmFuIiwgInRpdGxlIjogIlJva2EgRmVsbGEiLCAiZXhjZXJwdCI6ICIifSwgeyJkYXRlIjogIjIwMjMtMDUtMTIgMDg6MDA6MzMiLCAic2x1ZyI6ICJzcGljZS13b3JsZC1ob3Rwb3QiLCAidGl0bGUiOiAiU3BpY2UgV29ybGQgSG90cG90IiwgImV4Y2VycHQiOiAiIn0sIHsiZGF0ZSI6ICIyMDIzLTA1LTEyIDE0OjUzOjM5IiwgInNsdWciOiAiZ3JhbmQtbWFyYmxlIiwgInRpdGxlIjogIkdyYW5kIE1hcmJsZSIsICJleGNlcnB0IjogIlRoxrDGoW5nIGhp4buHdSBiw6FuaCBjYW8gY+G6pXAgR3JhbmQgTWFyYmxlIGPhu6dhIE5o4bqtdCBC4bqjbiB4deG6pXQgaGnhu4duIHThu6sgbsSDbSAxOTk2IHThuqFpIEt5b3RvIC0gdGjDoG5oIHBo4buRIGjGoW4gMTAwMCBuxINtIGzhu4tjaCBz4butIHbhu4EgbmfDoG5oIHRo4bunIGPDtG5nIG3hu7kgbmdo4buHLiJ9LCB7ImRhdGUiOiAiMjAyMy0wNS0xMiAxNTowMjo1NiIsICJzbHVnIjogInRoZS1icml4IiwgInRpdGxlIjogIlRoZSBCcml4IiwgImV4Y2VycHQiOiAiSWYgeW91IGZlZWwgb3ZlcndoZWxtZWQgYnkgdGhlIGJ1c3kgdXJiYW4gbGlmZSBhbmQgd2FudCB0byBmaW5kIGEgY29vbCwgZnJlc2ggcGxhY2UgdG8gcmVsYXgsIGRpbmUsIG9yIGNoYXQgd2l0aCBmcmllbmRzLCBjb21lIHRvIFRoZSBCcml4LiJ9LCB7ImRhdGUiOiAiMjAyMy0wNS0xMiAxNTowOTowOSIsICJzbHVnIjogImdvZG1vdGhlci1mcmllbmRzaGlwIiwgInRpdGxlIjogIkdvZE1vdGhlciBGcmllbmRzaGlwIiwgImV4Y2VycHQiOiAiQnJ1bmNoIGzDoCBi4buvYSDEg24ga+G6v3QgaOG7o3AgZ2nhu69hIHPDoW5nIHbDoCB0csawYSwgduG7q2Egbm8gxJHhu6cgbOG6oWkgduG7q2EgbGluaCBob+G6oXQuIFbDoG8gbsSDbSAyMDE5LCBraGkgU8OgaSBHw7JuIGNoxrBhIGPDsyBuaMOgIGjDoG5nIG7DoG8gdGhlbyBwaG9uZyBjw6FjaCBuw6B5LCBHb2RNb3RoZXIgcmEgxJHhu51pLCJ9LCB7ImRhdGUiOiAiMjAyMy0wNS0xMiAxNToxMjowMSIsICJzbHVnIjogImFtZGFuZy10eXBob29uIiwgInRpdGxlIjogIkFtZGFuZyBUeXBob29uIiwgImV4Y2VycHQiOiAiQW1kYW5nIFR5cGhvb24gbMOgIG3hu5l0IHRyb25nIG5o4buvbmcgbmjDoCBow6BuZyDEkcaw4bujYyBnaeG7m2kgdGhp4buHdSB0cm9uZyBNaWNoZWxpbiBHdWlkZSAyMDIxLiBOaMOgIGjDoG5nIG1hbmcgxJHhuq1tIGLhuqNuIHPhuq9jIOG6qW0gdGjhu7FjIFRydW5nIFF14buRYyB2w6AgVGjDoWkgTGFuLCDEkcOzIGPFqW5nIGzDoCJ9LCB7ImRhdGUiOiAiMjAyMy0wNS0xMiAxNToxMzoyOCIsICJzbHVnIjogInNreWxvZnQtYnktZ2xvdyIsICJ0aXRsZSI6ICJTa3lsb2Z0IGJ5IEdsb3ciLCAiZXhjZXJwdCI6ICJTa3lsb2Z0IGJ5IEdsb3cgU2t5bG9mdCBieSBHbG93IGlzIGEgcm9vZnRvcCBiYXIgbG9jYXRlZCBvbiB0aGUgUHJlc2lkZW50IFBsYWNlIGJ1aWxkaW5nIG9uIE5ndXllbiBEdSBzdHJlZXQsIERpc3RyaWN0IDEuIEl0IG9mZmVycyBhIDM2MC1kZWdyZWUgdmlldyBvZiB0aGUifSwgeyJkYXRlIjogIjIwMjMtMDUtMTkgMTc6MDc6NTIiLCAic2x1ZyI6ICJoZWl3YS1zdXNoaS1vbWFrYXNlIiwgInRpdGxlIjogIkhFSVdBIFNVU0hJIE9NQUtBU0UiLCAiZXhjZXJwdCI6ICJIZWl3YSBTdXNoaSBPbWFrYXNlIGzDoCDEkWnhu4NtIMSR4bq/biBraMO0bmcgdGjhu4MgYuG7jyBxdWEgY2hvIG5o4buvbmcgdMOtbiDEkeG7kyBj4bunYSDhuqltIHRo4buxYyBOaOG6rXQgQuG6o24gdOG6oWkgdGjDoG5oIHBo4buRIEjhu5MgQ2jDrSBNaW5oLiJ9LCB7ImRhdGUiOiAiMjAyMy0xMi0xNiAwMTowMDowMCIsICJzbHVnIjogImJhbWJpbm8tc2FpZ29uaG9yZWNhIiwgInRpdGxlIjogIkJhbWJpbm8gU3VwZXJDbHViIiwgImV4Y2VycHQiOiAiIn0sIHsiZGF0ZSI6ICIyMDIzLTEyLTI3IDA4OjAwOjAwIiwgInNsdWciOiAibXVhLWNyYWZ0LXNha2UtbGFtLXJ1b3Utc2FrZS1kYXUtdGllbi10YWktdmlldC1uYW0iLCAidGl0bGUiOiAiTcO5YSBDcmFmdCBTYWtlIiwgImV4Y2VycHQiOiAiTcO5YSBDcmFmdCBTYWtlIHThu7EgaMOgbyB24bubaSB0aGnhur90IGvhur8gbuG7mWkgdGjhuqV0IGN14buRbiBow7p0LCBuxqFpIGjDsmEgcXV54buHbiBnaeG7r2EgdGhp4bq/dCBr4bq/IGhp4buHbiDEkeG6oWkgdsOgIHRpbmggaG9hIOG6qW0gdGjhu7FjIE5o4bqtdCBC4bqjbi4ifSwgeyJkYXRlIjogIjIwMjQtMDEtMDQgMDI6MDg6MzkiLCAic2x1ZyI6ICJzb2wta2l0Y2hlbi1iYXItc2FpZ29uLWhvcmVjYSIsICJ0aXRsZSI6ICJTT0wgS0lUQ0hFTiAmIEJBUiIsICJleGNlcnB0IjogIlNPTCBLaXRjaGVuICZhbXA7IEJhciDEkcaw4bujYyBsaeG7h3Qga8OqIHRyb25nIGRhbmggc8OhY2ggMjAyMyBNSUNIRUxJTiBHdWlkZSBWaWV0bmFtIGRhbmggZ2nDoSwgduG7m2kgaMawxqFuZyB24buLIMSRYSBk4bqhbmcgaMOyYSBxdXnhu4duIHbhu5tpIHPhu7EgdGluaCB04bq/IGPhu6dhIMSR4bqndSBi4bq/cCBBZHJpYW4gQ2hvbmcgWWVuLiJ9LCB7ImRhdGUiOiAiMjAyNC0wMS0wNCAwMjoxNTo0OCIsICJzbHVnIjogInl1enUtb21ha2FzZSIsICJ0aXRsZSI6ICJZdXp1IE9tYWthc2UiLCAiZXhjZXJwdCI6ICJCxrDhu5tjIHbDoG8gdGjhur8gZ2nhu5tpIGPhu6dhIEJhbWJpbm8gU3VwZXJjbHViLCBuxqFpIMSRaeG7gXUgYsOsbmggdGjGsOG7nW5nIHRy4bufIG7Dqm4gcGhpIHRoxrDhu51uZywgdsOgIHRy4budaSDEkcOqbSBow7NhIHRow6JuIHRow6BuaCBkw7JuZyBuxINuZyBsxrDhu6NuZyBz4buRbmcgxJHhu5luZyBuaOG7i3AgbmjDoG5nLiJ9LCB7ImRhdGUiOiAiMjAyNC0wMS0yNiAxNzozNjo1NCIsICJzbHVnIjogImxpdHRsZS1iZWFyLXRoYW8tZGllbiIsICJ0aXRsZSI6ICJMSVRUTEUgQkVBUiIsICJleGNlcnB0IjogIkxpdHRsZSBCZWFyIFRo4bqjbyDEkGnhu4FuIG3hu51pIGLhuqFuIGLGsOG7m2MgdsOgbyBt4buZdCB0aGnDqm4gxJHGsOG7nW5nIOG6qW0gdGjhu7FjLCBnaWEgduG7iyB0aMahbSBuZ8OhdCB2w6AgdGnhur9uZyBjxrDhu51pIGjDsmEgcXV54buHbiB0aMOgbmggMSBi4bqjbiBnaWFvIGjGsOG7n25nIOG6pW0gY8O6bmcgY+G7p2EgaMawxqFuZyB24buLIHR1eeG7h3QgduG7nWkuIn0sIHsiZGF0ZSI6ICIyMDI0LTAyLTA0IDE1OjI2OjA0IiwgInNsdWciOiAiYmxpbmctYmxpbmctY2x1YiIsICJ0aXRsZSI6ICJCTElORyBCTElORyBDTFVCIiwgImV4Y2VycHQiOiAiQmxpbmcgQmxpbmcgQ2x1YiBsw6AgbeG7mXQgcXXDoW4gYmFyIG3hu5tpIMSRxrDhu6NjIHjDonkgZOG7sW5nIHbDoCBi4bqvdCDEkeG6p3UgaG/huqF0IMSR4buZbmcgdsOgbyBjdeG7kWkgbsSDbSAyMDIzLCBu4bqxbSB04bqhaSBz4buRIDc1IFh1w6JuIFRo4buneSwgVGjhuqNvIMSQaeG7gW4sIFF14bqtbiAyLCBUaMOgbmggcGjhu5EgSOG7kyBDaMOtIE1pbmguIn0sIHsiZGF0ZSI6ICIyMDI0LTAzLTI3IDA5OjM1OjI5IiwgInNsdWciOiAiZHUtYW4tYmVwLWNhbmctdGluLWNvbmctdHktbmhhdC1uaWNoaXlvIiwgInRpdGxlIjogIkThu7Egw6FuIGLhur9wIMSDbiBjw7RuZyBuZ2hp4buHcCBi4bq/cCBjxINuZyB0aW4gY8O0bmcgdHkgTmjhuq10IE5pY2hpeW8iLCAiZXhjZXJwdCI6ICJz4buxIGjhu6NwIHTDoWMgduG7m2kgU2FpZ29uIEhvcmVjYSDEkcOjIG1hbmcgbOG6oWkgbeG7mXQgYsaw4bubYyB0aeG6v24gbeG7m2kgbeG6uyBjaG8gTmljaGl5bywga2hpIGPDuW5nIG5oYXUgdOG6oW8gcmEgbeG7mXQgY8SDbiBi4bq/cCBjxINuZyB0aW4gxJFhIG7Eg25nIHRp4buHbiBuZ2hpIHbDoCBoaeG7h24gxJHhuqFpLCJ9LCB7ImRhdGUiOiAiMjAyNC0wNi0wNiAwODowMDowMCIsICJzbHVnIjogInJlbm92YXRlLXNvbC1raXRjaGVuLWJhci1xdWFuLTciLCAidGl0bGUiOiAiUmVub3ZhdGUgU09MIEtJVENIRU4gJiBCQVIgcXXhuq1uIDciLCAiZXhjZXJwdCI6ICJTYWlnb24gSG9yZWNhIHZpbmggaOG6oW5oIMSRxrDhu6NjIGNo4bunIMSR4bqndSB0xrAgU29sIEtpdGNoZW4gJmFtcDsgQmFyIHRpbiB0xrDhu59uZyBs4buxYSBjaOG7jW4gbMOgIMSRxqFuIHbhu4sgdXkgdMOtbiB0cmnhu4NuIGtoYWkgaOG7hyB0aOG7kW5nIGLhur9wIGNhbyBj4bqlcCBjaG8gbmjDoCBow6BuZyB04bqhaSBxdeG6rW4gNywgSENNQy4ifSwgeyJkYXRlIjogIjIwMjQtMDgtMTQgMTM6NTY6NDAiLCAic2x1ZyI6ICJkdS1hbi1rZGwtcnVuZy10aG9uZy1udWktdm9pLWN1YS1zYWlnb25ob3JlY2EiLCAidGl0bGUiOiAiS0RMIFLhu6pORyBUSMOUTkcgTsOaSSBWT0kiLCAiZXhjZXJwdCI6ICJTYWlnb24gSG9yZWNhIHbDoCBow6BuaCB0csOsbmgga2nhur9uIHThuqFvIOG6qW0gdGjhu7FjIFTDonkgTmd1ecOqbiB0aW5oIHThur8gdOG6oWkgS0RMIFLhu6tuZyBUaMO0bmcgTsO6aSBWb2kuIMKgxJBp4buDbSDEkeG6v24gdGjDuiB24buLIHThuqFpIMSQ4bupYyBUcuG7jW5nLiJ9LCB7ImRhdGUiOiAiMjAyNS0wMS0wMSAyMDo1NDowMiIsICJzbHVnIjogImJlcC1jYW50ZWVuLW5oYS1tYXktc2hlaC1mdW5nIiwgInRpdGxlIjogIlNIRUggRlVORyIsICJleGNlcnB0IjogIkThu7Egw6FuIMSR4bqzbmcgY+G6pXAgduG7m2kgY2FtIGvhur90IGhvw6BuIGjhuqNvIGPhu6dhIFNhaWdvbiBIb3JlY2EgdOG6oWkgQ2FudGVlbiBuaMOgIG3DoXkgU2hlaCBGdW5nIn0sIHsiZGF0ZSI6ICIyMDI1LTA0LTE2IDAyOjM3OjIyIiwgInNsdWciOiAidGhlLXJveWFsLWFsbC1kYXktZGluaW5nIiwgInRpdGxlIjogIlRIRSBST1lBTCAtIEFMTCBEQVkgRElOSU5HIiwgImV4Y2VycHQiOiAiTGl0dGxlIEJlYXIgVGjhuqNvIMSQaeG7gW4gbeG7nWkgYuG6oW4gYsaw4bubYyB2w6BvIG3hu5l0IHRoacOqbiDEkcaw4budbmcg4bqpbSB0aOG7sWMsIGdpYSB24buLIHRoxqFtIG5nw6F0IHbDoCB0aeG6v25nIGPGsOG7nWkgaMOyYSBxdXnhu4duIHRow6BuaCAxIGLhuqNuIGdpYW8gaMaw4bufbmcg4bqlbSBjw7puZyBj4bunYSBoxrDGoW5nIHbhu4sgdHV54buHdCB24budaS4ifSwgeyJkYXRlIjogIjIwMjUtMDQtMjAgMDA6MzY6MDciLCAic2x1ZyI6ICJnYW5oLWhhby1ub2ktaG9uLWJpZW4tdHJvbmctdHVuZy1uZXQta2llbi10cnVjIiwgInRpdGxlIjogIkfDoG5oIEjDoG8iLCAiZXhjZXJwdCI6ICJOaMOgIGjDoG5nIEfDoG5oIEjDoG8g4oCTIGPDoWkgdMOqbiDEkcOjIHRy4bufIG7Dqm4gcXVlbiB0aHXhu5ljIHbhu5tpIG5o4buvbmcgYWkgecOqdSBt4bq/biDhuqltIHRo4buxYyBiaeG7g24gVsWpbmcgVMOgdS4gQ8OzIGzhur0sIG5ndeG7k24gY+G6o20gaOG7qW5nIGzhu5tuIG5o4bqldCBj4bunYSBHw6BuaCBIw6BvIGNow61uaCBsw6AuLi4gYmnhu4NuIOKAkyJ9LCB7ImRhdGUiOiAiMjAyNS0wNC0yNSAwODoyMjowMCIsICJzbHVnIjogIm1hbS1tYW0tZWF0ZXJ5LWxvdW5nZS1uYW5nLXRhbS1tYW0tY29tLXZpZXQiLCAidGl0bGUiOiAiTcOCTSBNw4JNIEVBVEVSWSAmYW1wOyBMT1VOR0UiLCAiZXhjZXJwdCI6ICJU4burIMO9IHTGsOG7n25nIGto4bufaSBuZ3Xhu5NuIOKAkyDigJxtw6JtIGPGoW0gVmnhu4d04oCdIOKAkyBNw6JtIE3Dom0ga2jDqW8gbMOpbyDEkcawYSBiaeG7g3UgdMaw4bujbmcgc3VtIHbhuqd5IOG6pXkgbGVuIGzhu49pIGto4bqvcCBt4buNaSBuZ8OzYyBuZ8OhY2g6IHRyb25nIGjDrG5oIGTDoW5nIGNoaeG6v2MgYsOgbiwifSwgeyJkYXRlIjogIjIwMjUtMDUtMjIgMjM6MDg6NDQiLCAic2x1ZyI6ICJnLWN1cC1jb2ZmZWUtYmlzdHJvIiwgInRpdGxlIjogIkcuQ1VQIENPRkZFRSAmYW1wOyBCSVNUUk8iLCAiZXhjZXJwdCI6ICLigJxNw6JtIGPGoW3igJ0gYsaw4bubYyByYSB0aOG6vyBnaeG7m2kg4oCTIEtoaSBraMO0bmcgZ2lhbiBr4buDIGNodXnhu4duIHRy4bufIHRow6BuaCBj4bqndSBu4buRaSB2xINuIGjDs2EgVmnhu4d0In0sIHsiZGF0ZSI6ICIyMDI1LTEwLTAyIDE5OjQ0OjE3IiwgInNsdWciOiAiaGVtbWEtZGVzc2VydHMtbW90LWdvYy1uaG8tY2hhdS1hdS1naXVhLXRoYW8tZGllbiIsICJ0aXRsZSI6ICJIRU1NQSBERVNTRVJUUyIsICJleGNlcnB0IjogIlThu6sgw70gdMaw4bufbmcga2jhu59pIG5ndeG7k24g4oCTIOKAnG3Dom0gY8ahbSBWaeG7h3TigJ0g4oCTIE3Dom0gTcOibSBraMOpbyBsw6lvIMSRxrBhIGJp4buDdSB0xrDhu6NuZyBzdW0gduG6p3kg4bqleSBsZW4gbOG7j2kga2jhuq9wIG3hu41pIG5nw7NjIG5nw6FjaDogdHJvbmcgaMOsbmggZMOhbmcgY2hp4bq/YyBiw6BuLCJ9LCB7ImRhdGUiOiAiMjAyNS0xMC0wNCAxODo0NDo0MSIsICJzbHVnIjogImJlcC1hbi10cnVvbmctbWFtLW5vbi10dS10aHVjLXRyaW5oLXZ1b25nIiwgInRpdGxlIjogIlRSxq/hu5xORyBN4bqmTSBOT04gVFJJTkggVsavxqBORyIsICJleGNlcnB0IjogIk3hu5l0IGfDs2Mgbmjhu48gY2jDonUgw4J1IGdp4buvYSBUaOG6o28gxJBp4buBbiJ9LCB7ImRhdGUiOiAiMjAyNi0wMi0wMSAxNzoxNjoyMiIsICJzbHVnIjogInRoZS1jaGVlenktdGltZSIsICJ0aXRsZSI6ICJUaGUgQ2hlZXp5IFRpbWUiLCAiZXhjZXJwdCI6ICJHaeG7r2EgbMOybmcgQ+G6p24gVGjGoSDigJMgdsO5bmcgxJHhuqV0IMSRYW5nIGNodXnhu4NuIG3DrG5oIG3huqFuaCBt4bq9IOKAkyBUaGUgQ2hlZXp5IFRpbWUgY2jhu41uIG3hu5l0IGNvbiDEkcaw4budbmcgcuG6pXQgcmnDqm5nLiBLaMO0bmcgY2jhuqF5IHRoZW8gc+G7sSBow6BvIG5ob8OhbmcsIG5ow6AgaMOgbmcgbOG6pXkgY+G6o20gaOG7qW5nIHThu6sifSwgeyJkYXRlIjogIjIwMjYtMDMtMzAgMjM6MTU6MTYiLCAic2x1ZyI6ICJ0YWxlcy1ieS1jaGFwdGVyIiwgInRpdGxlIjogIlRhbGVzIGJ5IENoYXB0ZXIiLCAiZXhjZXJwdCI6ICJE4buxIMOhbiDEkeG6s25nIGPhuqVwIHbhu5tpIGNhbSBr4bq/dCBob8OgbiBo4bqjbyBj4bunYSBTYWlnb24gSG9yZWNhIHThuqFpIENhbnRlZW4gbmjDoCBtw6F5IFNoZWggRnVuZyJ9LCB7ImRhdGUiOiAiMjAyNi0wNC0wMSAyMjoyNDoyNiIsICJzbHVnIjogImNhc2EtbWFyaWEiLCAidGl0bGUiOiAiQ2FzYSBNYXJpYSIsICJleGNlcnB0IjogIkThu7Egw6FuIMSR4bqzbmcgY+G6pXAgduG7m2kgY2FtIGvhur90IGhvw6BuIGjhuqNvIGPhu6dhIFNhaWdvbiBIb3JlY2EgdOG6oWkgQ2FudGVlbiBuaMOgIG3DoXkgU2hlaCBGdW5nIn0sIHsiZGF0ZSI6ICIyMDI2LTA0LTAzIDE5OjI3OjE1IiwgInNsdWciOiAibW9hLW1vYSIsICJ0aXRsZSI6ICJNb2EgTW9hIiwgImV4Y2VycHQiOiAiROG7sSDDoW4gxJHhurNuZyBj4bqlcCB24bubaSBjYW0ga+G6v3QgaG/DoG4gaOG6o28gY+G7p2EgU2FpZ29uIEhvcmVjYSB04bqhaSBDYW50ZWVuIG5ow6AgbcOheSBTaGVoIEZ1bmcifSwgeyJkYXRlIjogIjIwMjYtMDQtMDUgMTk6Mzk6NDciLCAic2x1ZyI6ICJkdS1hbi12aW5oLWhpZXAiLCAidGl0bGUiOiAiROG7sSDDoW4gVsSpbmggSGnhu4dwIOKAkyBDb2ZmZWUgTGFiIiwgImV4Y2VycHQiOiAiROG7sSDDoW4gxJHhurNuZyBj4bqlcCB24bubaSBjYW0ga+G6v3QgaG/DoG4gaOG6o28gY+G7p2EgU2FpZ29uIEhvcmVjYSB04bqhaSBDYW50ZWVuIG5ow6AgbcOheSBTaGVoIEZ1bmcifSwgeyJkYXRlIjogIjIwMjYtMDQtMDcgMTk6NDU6NTEiLCAic2x1ZyI6ICJkdS1uYW0tYW4tYW4iLCAidGl0bGUiOiAiROG7sSBOYW0gQW4gQW4iLCAiZXhjZXJwdCI6ICJE4buxIMOhbiDEkeG6s25nIGPhuqVwIHbhu5tpIGNhbSBr4bq/dCBob8OgbiBo4bqjbyBj4bunYSBTYWlnb24gSG9yZWNhIHThuqFpIENhbnRlZW4gbmjDoCBtw6F5IFNoZWggRnVuZyJ9XQ=='), true) ?: [];

    echo "=== Import dự án (project) — " . ($is_run ? 'RUN' : 'DRY RUN') . " ===\n";
    echo "Tổng dự án trong gói: " . count($projects) . "\n\n";

    $created = 0; $skip = 0;
    foreach ($projects as $p) {
        $ex = get_page_by_path($p['slug'], OBJECT, 'project');
        if ($ex) { $skip++; echo "  · đã có: {$p['slug']}\n"; continue; }
        if (!$is_run) { $created++; echo "  + sẽ tạo: {$p['slug']}  ({$p['title']})\n"; continue; }
        $id = wp_insert_post([
            'post_type'      => 'project',
            'post_status'    => 'publish',
            'post_title'     => $p['title'],
            'post_name'      => $p['slug'],
            'post_excerpt'   => $p['excerpt'],
            'post_date'      => $p['date'],
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
        ], true);
        if (is_wp_error($id)) { echo "  ✗ LỖI {$p['slug']}: " . $id->get_error_message() . "\n"; }
        else { $created++; echo "  + {$p['slug']} → #$id\n"; }
    }
    echo "\n  --> " . ($is_run ? "tạo $created" : "dự kiến tạo $created") . ", bỏ qua $skip\n\n";

    echo "=== Chuẩn hoá trang Về / Liên hệ (template theme) ===\n";
    $pages = [
        've-saigon-horeca' => 'page-templates/page-gioi-thieu.php',
        'lien-he'          => 'page-templates/page-lien-he.php',
    ];
    foreach ($pages as $slug => $tpl) {
        $pg = get_page_by_path($slug, OBJECT, 'page');
        if (!$pg) { echo "  ! không thấy trang: $slug\n"; continue; }
        if ($is_run) {
            update_post_meta($pg->ID, '_wp_page_template', $tpl);
            update_post_meta($pg->ID, '_elementor_edit_mode', '');
        }
        echo "  $slug (#{$pg->ID}) " . ($is_run ? "→ $tpl" : "[dry]") . "\n";
    }

    if ($is_run) {
        flush_rewrite_rules(false);
        echo "\n✓ XONG. Mở /du-an/ + /ve-saigon-horeca/ + /lien-he/ kiểm tra.\n";
    } else {
        echo "\n→ chạy lại với &run=1 để áp dụng.\n";
    }
    exit;
});
