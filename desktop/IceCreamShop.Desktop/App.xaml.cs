using IceCreamShop.Desktop.Services;
using System.Configuration;
using System.Data;
using System.Windows;

namespace IceCreamShop.Desktop;

/// <summary>
/// Interaction logic for App.xaml
/// </summary>
public partial class App : Application
{
    public static ApiClient Api { get; } = new ApiClient();
}

