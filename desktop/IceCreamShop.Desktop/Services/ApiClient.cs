using System.Net.Http;
using System.Text.Json;
using System.Net.Http.Headers;
using System.Net.Http.Json;
using IceCreamShop.Desktop.Models;

namespace IceCreamShop.Desktop.Services;

public class ApiClient
{
    private readonly HttpClient _http = new HttpClient
    {
        BaseAddress = new Uri("http://127.0.0.1:8000/api/")
    };

    private readonly JsonSerializerOptions _json = new JsonSerializerOptions
    {
        PropertyNamingPolicy = JsonNamingPolicy.SnakeCaseLower
    };
    
    public User? CurrentUser { get; private set; }

    public ApiClient()
    {
        _http.DefaultRequestHeaders.Add("Accept", "application/json");
    }
    private async Task ThrowIfErrorAsync(HttpResponseMessage response)
    {
        if (response.IsSuccessStatusCode) return;

        var error = await response.Content.ReadFromJsonAsync<ApiError>(_json);
        throw new Exception(error?.Message ?? $"Request failed ({(int)response.StatusCode}).");
    }

    public async Task LoginAsync(string email, string password)
    {
        var response = await _http.PostAsJsonAsync("auth/login", new { email, password }, _json);
        await ThrowIfErrorAsync(response);

        var body = await response.Content.ReadFromJsonAsync<ApiResponse<LoginResult>>(_json);
        _http.DefaultRequestHeaders.Authorization =
            new AuthenticationHeaderValue("Bearer", body!.Data!.Token);
        CurrentUser = body.Data.User;
    }
    public async Task<List<Item>> GetItemsAsync(string search)
    {
        var url = $"items?per_page=100&search={Uri.EscapeDataString(search)}";
        var response = await _http.GetAsync(url);
        await ThrowIfErrorAsync(response);

        var body = await response.Content.ReadFromJsonAsync<ApiResponse<List<Item>>>(_json);
        return body?.Data ?? new List<Item>();
    }

        public async Task<List<Category>> GetCategoriesAsync()
    {
        var response = await _http.GetAsync("categories");
        await ThrowIfErrorAsync(response);

        var body = await response.Content.ReadFromJsonAsync<ApiResponse<List<Category>>>(_json);
        return body?.Data ?? new List<Category>();
    }

    public async Task CreateItemAsync(Item item)
    {
        var response = await _http.PostAsJsonAsync("items", item, _json);
        await ThrowIfErrorAsync(response);
    }

    public async Task UpdateItemAsync(Item item)
    {
        var response = await _http.PutAsJsonAsync($"items/{item.Id}", item, _json);
        await ThrowIfErrorAsync(response);
    }
}