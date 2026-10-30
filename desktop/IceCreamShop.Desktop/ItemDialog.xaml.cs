using System.Windows;
using IceCreamShop.Desktop.Models;

namespace IceCreamShop.Desktop;

public partial class ItemDialog : Window
{
    private readonly Item _item;

    public ItemDialog(Item? item = null)
    {
        InitializeComponent();
        _item = item ?? new Item();
        Title = item == null ? "Add item" : "Edit item";

        NameBox.Text = _item.Name;
        DescriptionBox.Text = _item.Description;
        PriceBox.Text = item == null ? "" : _item.Price.ToString();
        QuantityBox.Text = item == null ? "" : _item.Quantity.ToString();
    }

    private async void Window_Loaded(object sender, RoutedEventArgs e)
    {
        try
        {
            CategoryBox.ItemsSource = await App.Api.GetCategoriesAsync();
            CategoryBox.SelectedValue = _item.CategoryId;
        }
        catch (Exception ex)
        {
            ErrorText.Text = ex.Message;
        }
    }

    private async void SaveButton_Click(object sender, RoutedEventArgs e)
    {
        ErrorText.Text = "";

        if (!decimal.TryParse(PriceBox.Text, out var price) ||
            !int.TryParse(QuantityBox.Text, out var quantity))
        {
            ErrorText.Text = "Price and quantity must be numbers.";
            return;
        }

        _item.Name = NameBox.Text;
        _item.Description = DescriptionBox.Text;
        _item.CategoryId = CategoryBox.SelectedValue is int id ? id : 0;
        _item.Price = price;
        _item.Quantity = quantity;

        try
        {
            if (_item.Id == 0)
                await App.Api.CreateItemAsync(_item);
            else
                await App.Api.UpdateItemAsync(_item);

            DialogResult = true;
        }
        catch (Exception ex)
        {
            ErrorText.Text = ex.Message;
        }
    }
}